<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../entities/KategoriPelanggan.php';
require_once __DIR__ . '/../entities/Pelanggan.php';

class PelangganRepository extends BaseRepository
{
    protected string $table = 'm_pelanggan';

    public function save(Pelanggan $pelanggan) : false|Pelanggan
    {
        $saved = $this->basicSave([
            'nama' => $pelanggan->getNama(),
            'kontak' => $pelanggan->getKontak(),
            'akun_id' => $pelanggan->getAkun()->getId(),
            'kategori_id' => $pelanggan->getKategoriPelanggan()->getId(),
            'photo_id' => $pelanggan->getPhoto(),
            'photo_identitas' => $pelanggan->getPhotoIdentitas()
        ]);

        return $saved ? $pelanggan->setId($saved) : false;
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $row, bool $withRelations = false): Pelanggan
    {
        return new Pelanggan();
    }

}
