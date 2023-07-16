<?php

require_once __DIR__ . '/../repositories/PemesananRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../repositories/PelangganRepository.php';
require_once __DIR__ . '/../repositories/MobilRepository.php';
require_once __DIR__ . '/../repositories/DriverRepository.php';
require_once __DIR__ . '/../entities/Object/NomorPesanan.php';
require_once __DIR__ . '/../enums/StatusPemesanan.php';
require_once __DIR__ . '/../enums/StatusBuktiPembayaran.php';
require_once __DIR__ . '/PenyimpananService.php';
require_once __DIR__ .'/../repositories/KeberangkatanRepository.php';
require_once __DIR__ . '/PDFService.php';

class PemesananService
{
    private PemesananRepository $pemesananRepository;

    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    private DaerahOperasionalRepository $daerahOperasionalRepository;

    private PenyimpananService $penyimpananService;

    private MobilRepository $mobilRepository;

    private DriverRepository $driverRepository;
    private KeberangkatanRepository $keberangkatanRepository;

    private PelangganRepository $pelangganRepository;

    public function __construct()
    {
        $this->pemesananRepository = new PemesananRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
        $this->daerahOperasionalRepository = new DaerahOperasionalRepository();
        $this->penyimpananService = new PenyimpananService();
        $this->mobilRepository = new MobilRepository();
        $this->driverRepository = new DriverRepository();
        $this->keberangkatanRepository = new KeberangkatanRepository();
        $this->pelangganRepository = new PelangganRepository();
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
        DateTimeInterface    $tanggalKeberangkatan,
        int|JamKeberangkatan $jamKeberangkatan,
        Tiket                $tiket,
        int|Mobil            $mobil,
        array                $kursiDipesan, // @TODO: change to Object,
        bool                 $isRuteReversed = false
    ) : false|Pesanan {

        $nomorPesanan = $this->buatNomorPemesanan($tanggalKeberangkatan);

        if (is_int($jamKeberangkatan)) $jamKeberangkatan = $this->jamKeberangkatanRepository->findById($jamKeberangkatan);

        $asal = $tiket->getRute()->getAsal();
        $tujuan = $tiket->getRute()->getTujuan();

        if ($isRuteReversed) {
            $asal = $tiket->getRute()->getTujuan();
            $tujuan = $tiket->getRute()->getAsal();
        }

        $pesanan = new Pesanan();
        $pesanan->setNomorPesanan($nomorPesanan->getNomorPesanan());
        $pesanan->setNomorIterasiPesanan($nomorPesanan->getIterasi());
        $pesanan->setTanggalKeberangkatan($tanggalKeberangkatan);
        $pesanan->setTanggalPemesanan(date_create('now'));
        $pesanan->setJamKeberangkatan((string) $jamKeberangkatan);
        $pesanan->setKotaAsal($asal->getNamaKota());
        $pesanan->setKotaTujuan($tujuan->getNamaKota());
        $pesanan->setKategoriPelanggan($tiket->getKategoriPelanggan()->getKategori());
        $pesanan->setTotalTarif($tiket->getTarif() * count($kursiDipesan));
        $pesanan->setStatusBuktiPembayaran(StatusBuktiPembayaran::PENDING);
        $pesanan->setStatusPemesanan(StatusPemesanan::PENDING);
        $pesanan->setPemesanId(session()->auth()->getId());

        foreach ($kursiDipesan as $kursi) {
            $pesananDetail = new PesananDetail();
            $pesananDetail->setHargaTiket($tiket->getTarif());
            $pesananDetail->setNomorKursi((int) $kursi);

            $pesanan->addPesananDetail($pesananDetail);
        }

        return $this->pemesananRepository->save($pesanan);
    }

    public function cariPesananBerdasarkanNomorPesanan(string $nomor, bool $detail = true) : ?Pesanan
    {
        return $this->pemesananRepository->findByNomorPesanan($nomor, $detail);
    }

    public function cariPesananBerdasarkanNomorPesananDanPemesan(string $nomor, Akun $akun) : ?Pesanan
    {
        return $this->pemesananRepository->findByNomorPesananAndPelanggan($nomor, $akun);
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

    public function simpanBuktiPembayaran(string $nomorPesanan, string $nama, string $bank, int|float $nominal, array $bukti) : false|Pesanan
    {
        $pesanan = $this->pemesananRepository->findByNomorPesanan($nomorPesanan, true);

        if (false === $this->validasiNominalBayar($pesanan, $nominal)) return false;

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

    public function listPesananMenungguPembayaran(Akun $akun): array
    {
        return $this->pemesananRepository->getPesananMenungguPembayaran($akun, 10, 0);
    }

    public function validasiTanggalKeberangkatan(DateTimeInterface $tanggal): bool
    {
        $today = date("Y-m-d");

        return $today <= $tanggal->format("Y-m-d");
    }

    public function listKursiDipesanBerdasarkanTanggalDanRute(DateTimeInterface $tanggal, Rute $rute): array
    {
        return $this->pemesananRepository->getListKursiPesananByTanggalKeberangkatanAndRute($tanggal, $rute);
    }

    public function listPemesananHarianBerdasarkanDriver(string|Driver $driver) : array
    {
        return $this->pemesananRepository->getDailyPesananByDriver($this->driverRepository, $driver);
    }

    public function unduhBuktiPembayaran(string $nomorPemesanan, ?Akun $akun = null) : false|string
    {
        $file = $this->pemesananRepository->getBuktiPembayaran($nomorPemesanan, $akun);

        return $file ?? false;
    }

    public function validasiKursiDipesan(DateTimeInterface $tanggal, Rute $rute, int $mobilId, array $kursi) : bool
    {
        $mobil = null;
        if ($mobilId > 0) $mobil = $this->mobilRepository->findById($mobilId);

        return true;
    }

    public function listMobilDanKursiByDate(string $asal): array
    {
        $asal = $this->daerahOperasionalRepository->findById($asal);
        $listMobil = $this->keberangkatanRepository->getWhere(['provinsi_id' => $asal->getProvinsi()->value]);
        $listPesanan = $this->pemesananRepository->getListPesananHariIniByAsalKota($asal->getNamaKota());

        $result = [];
        /** @var $mobil Keberangkatan */
        foreach ($listMobil as $mobil) {
            $listKursiDipesan = [];
            for ($i = 1; $i <= $mobil->getMobil()->getJumlahKursi(); $i++) {
                $tersedia = ['nomor' => $i, 'tersedia' => true];

                foreach ($listPesanan as $pesanan) {
                    if(strtolower($mobil->getMobil()->getPlatNomor()) != strtolower($pesanan['mobil'] ?? '')) continue;

                    if (in_array($i, $pesanan['list_kursi_dipesan'])) $tersedia['tersedia'] = false;
                }

                $listKursiDipesan[] = $tersedia;
            }

            $result[$mobil->getMobil()->getId()] = [
                'mobil' => $mobil->getMobil()->toArray(),
                'list_kursi' => $listKursiDipesan
            ];
        }

        return array_values($result);
    }

    public function buatFileTiket(Pesanan $pesanan) : null|Pesanan
    {
        $pesanan = is_string($pesanan) ? $this->pemesananRepository->findByNomorPesanan($pesanan, true) : $pesanan;

        if (is_null($pesanan)) return null;
        if ($pesanan->getStatusBuktiPembayaran() !== StatusBuktiPembayaran::VALID) return null;
        if ($pesanan->getListKursi() < 1) $pesanan = $this->pemesananRepository->findByNomorPesanan($pesanan->getNomorPesanan());

        $pdf = new PDFService($this->penyimpananService);
        $tiket = $pdf->buatTiket($pesanan);
        $pesanan->setFileTiket($tiket);
        $this->pemesananRepository->updateInformasiFileTiket($pesanan);

        return $pesanan;
    }

    public function unduhTiket(string $nomorPemesanan, ?Akun $akun = null) : false|string
    {
        return $this->pemesananRepository->getFileTiket($nomorPemesanan, $akun);
    }

    public function cariBerdasarkanPelanggan(Pelanggan $pelanggan): false|array
    {
        if(!$this->pelangganRepository->exists($pelanggan)) return false;

        return $this->pemesananRepository->getByPemesan($pelanggan);
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

    private function validasiNominalBayar(Pesanan $pesanan, int|float $bayar) : bool
    {
        $minimum = $pesanan->getTotalTarif() / 2;

        return ($bayar >= $minimum) && ($bayar <= $pesanan->getTotalTarif());
    }
}
