<?php

require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../entities/KategoriPelanggan.php';
require_once __DIR__ . '/../entities/Pelanggan.php';
require_once __DIR__ . '/../repositories/PelangganRepository.php';
require_once __DIR__ . '/../services/AkunService.php';
require_once __DIR__ . '/../services/PenyimpananService.php';
require_once __DIR__ . '/../services/PemesananService.php';
require_once __DIR__ . '/../services/KategoriPelangganService.php';

class PelangganService
{

    private AkunService $akunService;

    private PelangganRepository $pelangganRepository;

    private KategoriPelangganService $kategoriPelangganService;

    private PemesananRepository $pemesananRepository;

    private PenyimpananService $penyimpananService;

    public function __construct()
    {
        $this->akunService = new AkunService();
        $this->pelangganRepository = new PelangganRepository();
        $this->kategoriPelangganService = new KategoriPelangganService();
        $this->penyimpananService = new PenyimpananService();
        $this->pemesananRepository = new PemesananRepository();
    }


    public function buatAkunPelanggan(string $nama, string $kontak, string $username, string $password) : false|Pelanggan
    {
        if ($this->akunService->cekApakahAkunTerdaftar($username)) return false;

        $akun = new Akun();
        $akun
            ->setUsername($username)
            ->setPassword(password_hash($password, PASSWORD_DEFAULT), true)
            ->setRole(Role::PELANGGAN)
        ;

        $akun = $this->akunService->buatAkunBaru($akun);
        if(false === $akun) return false;

        $pelanggan = new Pelanggan();
        $pelanggan->setNama($nama);
        $pelanggan->setKontak($kontak);
        $pelanggan->setAkun($akun);

        $kategoriPelanggan = $this->kategoriPelangganService->cari('umum');
        if(is_null($kategoriPelanggan)) return false;

        $pelanggan->setKategoriPelanggan($kategoriPelanggan);

        return $this->pelangganRepository->save($pelanggan);


    }

    public function listPelanggan(int $page = 1, ?string $search = null): false|array
    {
        if($search) return $this->pelangganRepository->findByNamaPelanggan($search);

        $total = 10;
        $offset = ($page - 1) * $total;

        return $this->pelangganRepository->get($total, $offset);
    }

    /** @doc jadwal adalah tiket yang telah dibayar diatas 1/2 total tarif dan/atau menunggu konfirmasi atau valid  */

    public function informasiSaya(Akun $akun): Pelanggan
    {
        return $this->pelangganRepository->getDetailByAkun($akun);
    }

    public function updateInformasiSaya(string $nama, ?string $kontak = null, int $kategoriPelanggan = -1, mixed $photoIdentitas = null) : false|Pelanggan
    {
        $pelanggan = $this->pelangganRepository->getDetailByAkun(session()->auth());

        $kategoriPelanggan = $this->kategoriPelangganService->cariById($kategoriPelanggan);
        if(is_null($kategoriPelanggan)) return false;

        if ($photoIdentitas && strtolower($kategoriPelanggan->getKategori()) != 'umum') {
            $photoIdentitas = $this->penyimpananService->simpanPhotoIdentitasPelanggan($photoIdentitas);
        }


        $pelanggan->setNama($nama);
        $pelanggan->setKontak($kontak ?? $pelanggan->getKontak());
        $pelanggan->setKategoriPelanggan($kategoriPelanggan);
        $pelanggan->setPhotoIdentitas($photoIdentitas);

        return $this->pelangganRepository->update($pelanggan);
    }

    public function daftarPemesananSaya(PemesananService $service, Pelanggan $pelanggan) : array
    {
        $listPesanan = $service->cariBerdasarkanPelanggan($pelanggan);

        $result = [
            'hari_ini' => [],
            'selesai' => [],
            'akan_datang' => [],
            'unconfirmed' => [],
            'menunggu_pembayaran' => [],
            'belum_lunas' => [],
            'semua' => $listPesanan
        ];

        /** @var $pesanan Pesanan */
        foreach ($listPesanan as $pesanan) {
            if ($this->isPesananToday($pesanan->getTanggalKeberangkatan())) $result['hari_ini'][] = $pesanan;

            if ($this->isDone($pesanan)) $result['selesai'][] = $pesanan;

            if($this->isPesananNextDay($pesanan->getTanggalKeberangkatan())) $result['akan_datang'][] = $pesanan;

            if($this->isUnconfirmed($pesanan)) $result['unconfirmed'][] = $pesanan;

            if($this->isUnPaid($pesanan)) $result['menunggu_pembayaran'][] = $pesanan;

            if($this->isPaidOnlyDownPayment($pesanan)) $result['belum_lunas'][] = $pesanan;
        }

        return $result;
    }

    public function semuaPesananSaya(array $filter) : array
    {
        $filter = [
            'nomor_pemesanan' => $filter['search'] ? "%" . $filter['search'] . "%" : null,
            'tanggal_keberangkatan' => !is_null($filter['date']) ? date_create($filter['date']) : null,
            'pagination' => [
                'offset' => ($filter['page'] - 1) * 10,
                'total' => 10
            ]
        ];

        return $this->pemesananRepository->getPesananPelangganByFilter(session()->auth(), $filter);
    }

    private function isPesananToday(DateTimeInterface $tanggal) : bool
    {
        return $tanggal->format('Y-m-d') == date('Y-m-d');
    }

    private function isDone(Pesanan $pesanan) : bool
    {
        return ($pesanan->getStatusPemesanan()->value == StatusPemesanan::SELESAI)
            && ($pesanan->getStatusBuktiPembayaran()->value == StatusBuktiPembayaran::VALID);
    }

    private function isPaidOnlyDownPayment(Pesanan $pesanan) : bool
    {
        return $pesanan->getTotalDibayarkan() < $pesanan->getTotalTarif();
    }

    private function isUnPaid(Pesanan $pesanan) : bool
    {
        return $pesanan->getStatusBuktiPembayaran()->value == StatusBuktiPembayaran::PENDING->value;
    }

    private function isUnconfirmed(Pesanan $pesanan) : bool
    {
        return $pesanan->getStatusBuktiPembayaran()->value == StatusBuktiPembayaran::UNCONFIRMED->value;
    }

    private function isPesananNextDay(DateTimeInterface $tanggal) : bool
    {
        return $tanggal->format('Y-m-d') > date('Y-m-d');
    }
};