<?php

require_once __DIR__ . '/../repositories/PemesananRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../entities/Object/NomorPesanan.php';
require_once __DIR__ . '/../enums/StatusPemesanan.php';
require_once __DIR__ . '/../enums/StatusBuktiPembayaran.php';
require_once __DIR__ . '/PenyimpananService.php';

class PemesananService
{
    private PemesananRepository $pemesananRepository;

    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    private PenyimpananService $penyimpananService;

    public function __construct()
    {
        $this->pemesananRepository = new PemesananRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
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

    public function cariPesananBerdasarkanNomorPesanan(string $nomor) : Pesanan
    {
        return $this->pemesananRepository->findByNomorPesanan($nomor, true);
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

    private function nomorPesaman(string $tahun, string $bulan, string $nomor): string
    {
        return sprintf("SWT/%s/%s/%s", $tahun, $bulan, str_pad($nomor, 6, "0", STR_PAD_LEFT));
    }

    private function createNomorPesananObject(string $nomor, int $iterasi) : NomorPesanan
    {
        return new NomorPesanan($nomor, $iterasi);
    }
}
