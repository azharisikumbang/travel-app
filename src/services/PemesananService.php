<?php

require_once __DIR__ . '/../repositories/PemesananRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../entities/Object/NomorPesanan.php';
require_once __DIR__ . '/../enums/StatusPemesanan.php';
require_once __DIR__ . '/../enums/StatusBuktiPembayaran.php';
require_once __DIR__ . '/PenyimpananService.php';

class PemesananService
{
    private PemesananRepository $pemesananRepository;

    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    private DaerahOperasionalRepository $daerahOperasionalRepository;

    private PenyimpananService $penyimpananService;

    public function __construct()
    {
        $this->pemesananRepository = new PemesananRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
        $this->daerahOperasionalRepository = new DaerahOperasionalRepository();
        $this->penyimpananService = new PenyimpananService();
    }

    public function buatNomorPemesanan(DateTimeInterface $tanggal) : NomorPesanan
    {
        $latest = $this->pemesananRepository->getLatest();

        $iterasi = 1;
        if(null === $latest) {
            $nomorPesanan = $this->nomorPesaman($tanggal->format("Y"), $tanggal->format("m"), 1);

            return $this->createNomorPesananObject($nomorPesanan, $iterasi);
        }

        $iterasi = $latest->getNomorIterasiPesanan() + 1;
        $nomorPesanan = $this->nomorPesaman($tanggal->format("Y"), $tanggal->format("m"), $latest->getNomorIterasiPesanan() + 1);

        return $this->createNomorPesananObject($nomorPesanan, $iterasi);
    }

    public function buatPesananBaru(
        NomorPesanan $nomorPesanan,
        DateTimeInterface $tanggalKeberangkatan,
        int|JamKeberangkatan $jamKeberangkatan,
        Tarif $tarif,
        array $kursiDipesan // @TODO: change to Object
    ) : Pesanan {
        $jamKeberangkatan = !($jamKeberangkatan instanceof JamKeberangkatan) ? $this->jamKeberangkatanRepository->findById($jamKeberangkatan) : $jamKeberangkatan;

        $pesanan = new Pesanan();
        $pesanan
            ->setTipePenumpang($tarif->getTipePenumpang()->getTipePenumpang())
            ->setNomorPesanan($nomorPesanan->getNomorPesanan())
            ->setNomorIterasiPesanan($nomorPesanan->getIterasi())
            ->setTanggalKeberangkatan($tanggalKeberangkatan)
            ->setJamKeberangkatan($jamKeberangkatan->getJam(true))
            ->setKotaAsal($tarif->getAsal()->getNamaKota())
            ->setKotaTujuan($tarif->getTujuan()->getNamaKota())
            ->setStatusPemesanan(StatusPemesanan::PENDING)
            ->setStatusBuktiPembayaran(StatusBuktiPembayaran::PENDING)
        ;

        foreach ($kursiDipesan as $kursi) $pesanan->addNomorKursi($kursi, $tarif);

        return $this->pemesananRepository->save($pesanan);
    }

    public function cariPesananBerdasarkanNomorPesanan(string $nomor, bool $detail = true) : ?Pesanan
    {
        return $this->pemesananRepository->findByNomorPesanan($nomor, $detail);
    }

    public function simpanInformasiPemesan(string $nomorPesanan, string $nama, string $kontak, string $titik_jemput) : false|Pesanan
    {
        $pesanan = $this->pemesananRepository->findByNomorPesanan($nomorPesanan, true);

        if(is_null($pesanan)) return false;

        $pesanan
            ->setNamaPemesan($nama)
            ->setKontakPemesan($kontak)
            ->setTitikJemput($titik_jemput);

        $this->pemesananRepository->updateInformasiPemesanan($pesanan);

        return $pesanan;
    }

    public function simpanBuktiPembayaran(string $nomorPesanan, string $nama, string $bank, string $nominal, array $bukti) : false|Pesanan
    {
        $pesanan = $this->pemesananRepository->findByNomorPesanan($nomorPesanan, true);

        if(is_null($pesanan)) return false;

        if ($pesanan->getTotalTarif() != $nominal) return false;

        $buktiFileName = $this->penyimpananService->simpanBuktiPembayaran($bukti);

        $pesanan
            ->setNamaPembayaran($nama)
            ->setBankPembayaran($bank)
            ->setBuktiPembayaran($buktiFileName)
            ->setTotalDibayarkan($nominal)
            ->setStatusBuktiPembayaran(StatusBuktiPembayaran::UNCONFIRMED)
        ;

        $this->pemesananRepository->saveInformasiPembayaran($pesanan);

        return $pesanan;
    }

    public function listPesananBerdasarkanHari(int $total, int $from): array
    {
        $listPesanan = $this->pemesananRepository->get($total, $from, 'tanggal_keberangkatan', 'desc');

        $hariIni = date('Y-m-d');
        $listPesananHariIni = [];
        foreach ($listPesanan as $index => $pesanan) {
            if($hariIni != $pesanan->getTanggalKeberangkatan()->format('Y-m-d')) continue;

            $listPesananHariIni[] = $pesanan;
            unset($listPesanan[$index]);
        }

        return ['hari_ini' => $listPesananHariIni, 'lainnya' => array_values($listPesanan)];
    }

    public function listPesananBerdasarkanTanggalKeberangkatan(DateTimeInterface $tanggal, int $total = 10, int $from = 0) : array
    {
        return array_map(function ($item) {
            /** @var $item Pesanan */
            return [
                'data' => [
                    'pesanan' => [
                        'nomor' => $item->getNomorPesanan(),
                        'tanggal_keberangkatan' => $item->getTanggalKeberangkatan()->format("Y-m-d"),
                        'jam_keberangkatan' => $item->getJamKeberangkatan(),
                        'jadwal_lengkap' => $item->getJadwalLengkap(),
                        'status_pemesanan' => [
                            'value' => $item->getStatusPemesanan()->value,
                            'color' => $item->getStatusPemesanan()->getColor()
                        ],
                        'status_pembayaran' => [
                            'value' => $item->getStatusBuktiPembayaran()->value,
                            'display' => $item->getStatusBuktiPembayaran()->getDisplayName(),
                            'color' => $item->getStatusBuktiPembayaran()->getColor()
                        ]
                    ],
                    'pemesan' => [
                        'nama' => $item->getNamaPemesan(),
                        'kontak' => $item->getKontakPemesan(),
                        'titik_jemput' => $item->getTitikJemput()
                    ],
                    'keberangkatan' => [
                        'asal' => $item->getKotaAsal(),
                        'tujuan' => $item->getKotaTujuan(),
                        'full' => $item->getRute()
                    ]
                ],
                'link_detail' => ''
            ];
        }, $this->pemesananRepository->findByTanggalKeberangkatan($tanggal, $total, $from));
    }

    public function listPemesananBerdasarkan(string $tanggal, int $asal, int $tujuan, int $total = 10, int $from = 0): array
    {
        $tanggal = ($tanggal == "_") ? null: date_create($tanggal);
        $asal = ($asal == -1) ? null: $this->daerahOperasionalRepository->findById($asal);
        $tujuan = ($tujuan == -1) ? null: $this->daerahOperasionalRepository->findById($tujuan);

        $pesanan = $this->pemesananRepository->filterPesanan($tanggal, $asal, $tujuan, $total, $from);

        return array_map(function ($item) {
            /** @var $item Pesanan */
            return [
                'data' => [
                    'pesanan' => [
                        'nomor' => $item->getNomorPesanan(),
                        'tanggal_keberangkatan' => $item->getTanggalKeberangkatan()->format("Y-m-d"),
                        'jam_keberangkatan' => $item->getJamKeberangkatan(),
                        'jadwal_lengkap' => $item->getJadwalLengkap(),
                        'status_pemesanan' => [
                            'value' => $item->getStatusPemesanan()->value,
                            'color' => $item->getStatusPemesanan()->getColor()
                        ],
                        'status_pembayaran' => [
                            'value' => $item->getStatusBuktiPembayaran()->value,
                            'display' => $item->getStatusBuktiPembayaran()->getDisplayName(),
                            'color' => $item->getStatusBuktiPembayaran()->getColor()
                        ]
                    ],
                    'pemesan' => [
                        'nama' => $item->getNamaPemesan(),
                        'kontak' => $item->getKontakPemesan(),
                        'titik_jemput' => $item->getTitikJemput()
                    ],
                    'keberangkatan' => [
                        'asal' => $item->getKotaAsal(),
                        'tujuan' => $item->getKotaTujuan(),
                        'full' => $item->getRute()
                    ]
                ],
                'link_detail' => ''
            ];
        }, $pesanan);
    }

    public function listPesananBerdasarkanNomorPesanan(string $nomor, int $total = 10, int $from = 0) : array
    {
        $pesanan = $this->pemesananRepository->findByNomorPesanan($nomor, false);

        if (is_null($pesanan)) return [];

        return [
            'data' => [
                'pesanan' => [
                    'nomor' => $pesanan->getNomorPesanan(),
                    'tanggal_keberangkatan' => $pesanan->getTanggalKeberangkatan()->format("Y-m-d"),
                    'jam_keberangkatan' => $pesanan->getJamKeberangkatan(),
                    'jadwal_lengkap' => $pesanan->getJadwalLengkap(),
                    'status_pemesanan' => [
                        'value' => $pesanan->getStatusPemesanan()->value,
                        'color' => $pesanan->getStatusPemesanan()->getColor()
                    ],
                    'status_pembayaran' => [
                        'value' => $pesanan->getStatusBuktiPembayaran()->value,
                        'display' => $pesanan->getStatusBuktiPembayaran()->getDisplayName(),
                        'color' => $pesanan->getStatusBuktiPembayaran()->getColor()
                    ]
                ],
                'pemesan' => [
                    'nama' => $pesanan->getNamaPemesan(),
                    'kontak' => $pesanan->getKontakPemesan(),
                    'titik_jemput' => $pesanan->getTitikJemput()
                ],
                'keberangkatan' => [
                    'asal' => $pesanan->getKotaAsal(),
                    'tujuan' => $pesanan->getKotaTujuan(),
                    'full' => $pesanan->getRute()
                ]
            ],
            'link_detail' => ''
        ];
    }

    public function listJadwalBerdasarkan(string $tanggal, int $asal, int $tujuan, int $total = 10, int $from = 0): array
    {
        $tanggal = ($tanggal == "_") ? null: date_create($tanggal);
        $asal = ($asal == -1) ? null: $this->daerahOperasionalRepository->findById($asal);
        $tujuan = ($tujuan == -1) ? null: $this->daerahOperasionalRepository->findById($tujuan);

        $pesanan = $this->pemesananRepository->filterJadwal($tanggal, $asal, $tujuan, $total, $from);

        return array_map(function ($item) {
            /** @var $item Pesanan */
            return [
                'data' => [
                    'pesanan' => [
                        'nomor' => $item->getNomorPesanan(),
                        'tanggal_keberangkatan' => $item->getTanggalKeberangkatan()->format("Y-m-d"),
                        'jam_keberangkatan' => $item->getJamKeberangkatan(),
                        'jadwal_lengkap' => $item->getJadwalLengkap(),
                        'status_pemesanan' => [
                            'value' => $item->getStatusPemesanan()->value,
                            'color' => $item->getStatusPemesanan()->getColor()
                        ],
                        'status_pembayaran' => [
                            'value' => $item->getStatusBuktiPembayaran()->value,
                            'display' => $item->getStatusBuktiPembayaran()->getDisplayName(),
                            'color' => $item->getStatusBuktiPembayaran()->getColor()
                        ]
                    ],
                    'pemesan' => [
                        'nama' => $item->getNamaPemesan(),
                        'kontak' => $item->getKontakPemesan(),
                        'titik_jemput' => $item->getTitikJemput()
                    ],
                    'keberangkatan' => [
                        'asal' => $item->getKotaAsal(),
                        'tujuan' => $item->getKotaTujuan(),
                        'full' => $item->getRute()
                    ]
                ],
                'link_detail' => ''
            ];
        }, $pesanan);
    }

    public function konfirmasiPembayaran(Pesanan $pesanan, string|StatusBuktiPembayaran $status) : false|Pesanan
    {
        if(false === $this->isStatusBuktiPembayaranConfirmable($pesanan)) return false;

        $status = (is_string($status)) ? StatusBuktiPembayaran::fromLabel($status) : $status;

        $pesanan->setStatusBuktiPembayaran($status);
        $this->pemesananRepository->updateStatusPembayaran($pesanan, $status);

        return $pesanan;
    }

    public function listJadwalPerjalananPelanggan(Akun $user, int $total = 10, $from = 0): array
    {
        $listPesanan = $this->pemesananRepository->getJadwalKeberangkatanByPemesanId($user, $total, $from);

        $hariIni = date('Y-m-d');
        $listPesananHariIni = [];
        foreach ($listPesanan as $index => $pesanan) {
            if($hariIni != $pesanan->getTanggalKeberangkatan()->format('Y-m-d')) continue;

            $listPesananHariIni[] = $pesanan;
            unset($listPesanan[$index]);
        }

        return ['hari_ini' => $listPesananHariIni, 'lainnya' => array_values($listPesanan)];
    }

    private function isStatusBuktiPembayaranConfirmable(Pesanan $pesanan): bool
    {
        return $pesanan->getStatusBuktiPembayaran() == StatusBuktiPembayaran::UNCONFIRMED;
    }

    private function nomorPesaman(string $tahun, string $bulan, string $nomor): string
    {
        return sprintf("SWT/%s/%s/%s", $tahun, $bulan, str_pad($nomor, 6, "0", STR_PAD_LEFT));
    }

    private function createNomorPesananObject(string $nomor, int $iterasi) : NomorPesanan
    {
        return new NomorPesanan($nomor, $iterasi);
    }
}
