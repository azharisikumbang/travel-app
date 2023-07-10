<?php

require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../entities/KategoriPelanggan.php';
require_once __DIR__ . '/../entities/Pelanggan.php';
require_once __DIR__ . '/../repositories/PelangganRepository.php';
require_once __DIR__ . '/../services/AkunService.php';
require_once __DIR__ . '/../services/KategoriPelangganService.php';

class PelangganService
{

    private AkunService $akunService;

    private PelangganRepository $pelangganRepository;

    private KategoriPelangganService $kategoriPelangganService;

    public function __construct()
    {
        $this->akunService = new AkunService();
        $this->pelangganRepository = new PelangganRepository();
        $this->kategoriPelangganService = new KategoriPelangganService();
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
};