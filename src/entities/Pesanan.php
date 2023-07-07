<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
require_once __DIR__ . '/../enums/StatusPemesanan.php';
require_once __DIR__ . '/../enums/StatusBuktiPembayaran.php';
require_once __DIR__ . '/PesananDetail.php';

class Pesanan implements EntityInterface
{
    private int $id;

    private string $nomorPesanan;

    private int $nomorIterasiPesanan;

    private ?int $pemesanId = null;

    private ?string $namaPemesan = null;

    private ?string $kontakPemesan = null;

    private DateTimeInterface $tanggalPemesanan;

    private DateTimeInterface $tanggalKeberangkatan;

    private string $jamKeberangkatan;

    private string $kotaAsal;

    private string $kotaTujuan;

    private string $tipePenumpang;

    private ?string $titikJemput = null;

    private float $totalTarif = 0;

    private float $totalUangMuka = 0;

    private float $totalDibayarkan = 0;

    private ?string $buktiPembayaran = null;

    private ?string $namaPembayaran = null;

    private ?string $bankPembayaran = null;

    private StatusBuktiPembayaran $statusBuktiPembayaran;

    private StatusPemesanan $statusPemesanan;

    private ?int $mobilId = null;

    private array $listKursi;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNomorPesanan(): string
    {
        return $this->nomorPesanan;
    }

    /**
     * @return int
     */
    public function getNomorIterasiPesanan(): int
    {
        return $this->nomorIterasiPesanan;
    }

    /**
     * @return int
     */
    public function getPemesanId(): ?int
    {
        return $this->pemesanId;
    }


    /**
     * @return string
     */
    public function getNamaPemesan(): string
    {
        return $this->namaPemesan ?? "";
    }

    /**
     * @return string
     */
    public function getKontakPemesan(): string
    {
        return $this->kontakPemesan ?? "";
    }

    /**
     * @return DateTimeInterface
     */
    public function getTanggalPemesanan(): DateTimeInterface
    {
        return $this->tanggalPemesanan;
    }

    /**
     * @return DateTimeInterface
     */
    public function getTanggalKeberangkatan(): DateTimeInterface
    {
        return $this->tanggalKeberangkatan;
    }

    /**
     * @return string
     */
    public function getJamKeberangkatan(): string
    {
        return $this->jamKeberangkatan;
    }

    public function getJadwalLengkap(): string
    {
        return sprintf("%s %s WIB", $this->getTanggalKeberangkatan()->format('d/m/Y'), $this->getJamKeberangkatan());
    }

    /**
     * @return string
     */
    public function getKotaAsal(): string
    {
        return $this->kotaAsal;
    }

    /**
     * @return string
     */
    public function getKotaTujuan(): string
    {
        return $this->kotaTujuan;
    }

    public function getRute(): string
    {
        return sprintf("%s - %s", $this->getKotaAsal(), $this->getKotaTujuan());
    }

    /**
     * @return string
     */
    public function getTipePenumpang(): string
    {
        return $this->tipePenumpang;
    }

    /**
     * @return string
     */
    public function getTitikJemput(): string
    {
        return $this->titikJemput ?? "";
    }

    /**
     * @return float
     */
    public function getTotalTarif(): float
    {
        return array_sum(array: array_map(fn($item) => $item->getHargaTiket(), $this->getListKursi()));
    }

    /**
     * @return float
     */
    public function getTotalUangMuka(): float
    {
        return $this->totalUangMuka;
    }

    /**
     * @return float
     */
    public function getTotalDibayarkan(): float
    {
        return $this->totalDibayarkan;
    }

    /**
     * @return string|null
     */
    public function getBuktiPembayaran(): ?string
    {
        return $this->buktiPembayaran;
    }

    /**
     * @return string|null
     */
    public function getNamaPembayaran(): ?string
    {
        return $this->namaPembayaran;
    }

    /**
     * @return string|null
     */
    public function getBankPembayaran(): ?string
    {
        return $this->bankPembayaran;
    }

    /**
     * @return string
     */
    public function getStatusBuktiPembayaran(): StatusBuktiPembayaran
    {
        return $this->statusBuktiPembayaran;
    }

    /**
     * @return string
     */
    public function getStatusPemesanan(): StatusPemesanan
    {
        return $this->statusPemesanan;
    }

    /**
     * @return int
     */
    public function getMobilId(): ?int
    {
        return $this->mobilId;
    }

    /**
     * @return array
     */
    public function getListKursi(): array
    {
        return $this->listKursi;
    }

    /**
     * @param string $nomorPesanan
     */
    public function setNomorPesanan(string $nomorPesanan): self
    {
        $this->nomorPesanan = $nomorPesanan;

        return $this;
    }

    /**
     * @param int $nomorIterasiPesanan
     */
    public function setNomorIterasiPesanan(int $nomorIterasiPesanan): self
    {
        $this->nomorIterasiPesanan = $nomorIterasiPesanan;

        return $this;
    }

    /**
     * @param int $pemesanId
     */
    public function setPemesanId(?int $pemesanId): self
    {
        $this->pemesanId = $pemesanId;

        return $this;
    }

    /**
     * @param string $namaPemesan
     */
    public function setNamaPemesan(string $namaPemesan): self
    {
        $this->namaPemesan = $namaPemesan;

        return $this;
    }

    /**
     * @param string $kontakPemesan
     */
    public function setKontakPemesan(string $kontakPemesan): self
    {
        $this->kontakPemesan = $kontakPemesan;

        return $this;
    }

    /**
     * @param DateTimeInterface $tanggalPemesanan
     */
    public function setTanggalPemesanan(DateTimeInterface $tanggalPemesanan): self
    {
        $this->tanggalPemesanan = $tanggalPemesanan;

        return $this;
    }

    /**
     * @param DateTimeInterface $tanggalKeberangkatan
     */
    public function setTanggalKeberangkatan(DateTimeInterface $tanggalKeberangkatan): self
    {
        $this->tanggalKeberangkatan = $tanggalKeberangkatan;

        return $this;
    }

    /**
     * @param string $jamKeberangkatan
     */
    public function setJamKeberangkatan(string $jamKeberangkatan): self
    {
        $this->jamKeberangkatan = $jamKeberangkatan;

        return $this;
    }

    /**
     * @param string $kotaAsal
     */
    public function setKotaAsal(string $kotaAsal): self
    {
        $this->kotaAsal = $kotaAsal;

        return $this;
    }

    /**
     * @param string $kotaTujuan
     */
    public function setKotaTujuan(string $kotaTujuan): self
    {
        $this->kotaTujuan = $kotaTujuan;

        return $this;
    }

    /**
     * @param string $tipePenumpang
     */
    public function setTipePenumpang(string $tipePenumpang): self
    {
        $this->tipePenumpang = $tipePenumpang;

        return $this;
    }

    /**
     * @param string $titikJemput
     */
    public function setTitikJemput(string $titikJemput): self
    {
        $this->titikJemput = $titikJemput;

        return $this;
    }

    /**
     * @param float $totalTarif
     */
    public function setTotalTarif(float $totalTarif): self
    {
        $this->totalTarif = $totalTarif;

        return $this;
    }

    /**
     * @param float $totalUangMuka
     */
    public function setTotalUangMuka(float $totalUangMuka): self
    {
        $this->totalUangMuka = $totalUangMuka;

        return $this;
    }

    /**
     * @param float $totalDibayarkan
     */
    public function setTotalDibayarkan(float $totalDibayarkan): self
    {
        $this->totalDibayarkan = $totalDibayarkan;

        return $this;
    }

    /**
     * @param string|null $buktiPembayaran
     */
    public function setBuktiPembayaran(?string $buktiPembayaran): self
    {
        $this->buktiPembayaran = $buktiPembayaran;

        return $this;
    }

    /**
     * @param string|null $namaPembayaran
     */
    public function setNamaPembayaran(?string $namaPembayaran): self
    {
        $this->namaPembayaran = $namaPembayaran;

        return $this;
    }

    /**
     * @param string|null $bankPembayaran
     */
    public function setBankPembayaran(?string $bankPembayaran): self
    {
        $this->bankPembayaran = $bankPembayaran;

        return $this;
    }

    /**
     * @param string $statusBuktiPembayaran
     */
    public function setStatusBuktiPembayaran(StatusBuktiPembayaran $statusBuktiPembayaran): self
    {
        $this->statusBuktiPembayaran = $statusBuktiPembayaran;

        return $this;
    }

    /**
     * @param StatusPemesanan $statusPemesanan
     */
    public function setStatusPemesanan(StatusPemesanan $statusPemesanan): self
    {
        $this->statusPemesanan = $statusPemesanan;
        
        return $this;
    }

    public function addPesananDetail(PesananDetail $detail) : self
    {
        if(!empty($this->listKursi))
            foreach ($this->listKursi as $kursi)
                if($kursi->getNomorKursi() == $detail->getNomorKursi()) return $this;

        $this->listKursi[] = $detail;

        return $this;
    }

    public function addNomorKursi(int $nomor, Tarif $tarif, int $id = null) : self
    {
        $pesananDetail = new PesananDetail();
        $pesananDetail->setHargaTiket($tarif->getTarif());
        $pesananDetail->setNomorKursi($nomor);
//        $pesananDetail->setPesananId($this->getId());

        if (false === is_null($id)) $pesananDetail->setId($id);

        $this->updateTotalTarif($pesananDetail);
        $this->addPesananDetail($pesananDetail);

        return $this;
    }

    private function updateTotalTarif(PesananDetail $detail) : void
    {
        $this->totalTarif += $detail->getHargaTiket();
    }

    /**
     * @param int $mobilId
     */
    public function setMobilId(?int $mobilId): self
    {
        $this->mobilId = $mobilId;

        return $this;
    }

    public function toArray(): array
    {
        return [
//            'id' => $this->getId(),
            'nomor_pemesanan' => $this->getNomorPesanan(),
            'nomor_iterasi_pesanana' => $this->getNomorIterasiPesanan(),
            'nama_pemesanan' => $this->getNamaPemesan(),
            'kontak_pemesanan' => $this->getKontakPemesan(),
            'tanggal_keberangkatan' => $this->getTanggalKeberangkatan()->format('Y-m-d'),
            'jam_keberangkatan' => $this->getJamKeberangkatan(),
            'kota_asal' => $this->getKotaAsal(),
            'kota_tujuan' => $this->getKotaTujuan(),
            'titik_jemput' => $this->getTitikJemput(),
            'tipe_penumpang' => $this->getTipePenumpang(),
            'total_tarif' => $this->getTotalTarif(),
            'status_bukti_pembayaran' => $this->getStatusBuktiPembayaran()->value,
            'status_pemesanan' => $this->getStatusPemesanan()->value,
            'total_uang_muka' => $this->getTotalUangMuka(),
            'total_dibayarkan' => $this->getTotalDibayarkan(),
            'bukti_pembayaran' => $this->getBuktiPembayaran(),
            'nama_pembayaran' => $this->getNamaPembayaran(),
            'bank_pembayaran' => $this->getBankPembayaran(),
            'pemesan_id' => $this->getPemesanId(),
            'mobil_id' => $this->getMobilId(),
            'list_kursi_dipesan' => array_map(fn($item) => $item->toArray(), $this->getListKursi()),
            'keberangkatan' => $this->getRute(),
            'jadwal' => $this->getJadwalLengkap(),
            'tanggal_pemesanan' => $this->getTanggalPemesanan()->format('Y-m-d')
        ];
    }
}