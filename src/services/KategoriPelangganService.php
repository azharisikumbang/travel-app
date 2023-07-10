<?php

require_once __DIR__ . '/../repositories/KategoriPelangganRepository.php';
require_once __DIR__ . '/../entities/KategoriPelanggan.php';

class KategoriPelangganService
{
    private KategoriPelangganRepository $kategoriPelangganRepository;

    public function __construct()
    {
        $this->kategoriPelangganRepository = new KategoriPelangganRepository();
    }

    public function simpan(int $id, string $kategori) : false|KategoriPelanggan
    {
        $kategoriPelanggan = new KategoriPelanggan();
        $kategoriPelanggan->setId($id);
        $kategoriPelanggan->setKategori($kategori);

        return $this->kategoriPelangganRepository->updateOrCreate($kategoriPelanggan);
    }

    public function listKategoriPelanggan(int $length = 10, int $from = 0): array
    {
        return $this->kategoriPelangganRepository->get($length, $from);
    }

    public function hapus(int|KategoriPelanggan $kategoriPelanggan) : bool
    {
        return $this->kategoriPelangganRepository->deleteById($kategoriPelanggan);
    }

    public function cari(string $kategori) : ?KategoriPelanggan
    {
        return $this->kategoriPelangganRepository->getWhere('kategori', $kategori);
    }
}