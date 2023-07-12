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

    public function findByNamaPelanggan(string $search) : array
    {
        return $this->getByQuery(
            "SELECT p.*, a.username, k.kategori
                FROM {$this->getTable()} p 
                JOIN m_akun a ON a.id = p.akun_id
                JOIN m_kategori_pelanggan k ON k.id = p.kategori_id
                WHERE p.nama LIKE :nama",
            ['nama' => "%". $search . "%"],
            true
        );
    }

    public function get(int $total, int $offset) : array
    {
        return $this->getByQuery(
            "SELECT p.*, a.username, k.kategori
                FROM {$this->getTable()} p 
                JOIN m_akun a ON a.id = p.akun_id
                JOIN m_kategori_pelanggan k ON k.id = p.kategori_id
                LIMIT $offset, $total",
            [],
            true
        );
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $row, bool $withRelations = false): Pelanggan
    {
        $akun = (new Akun())
            ->setId($row['akun_id'])
            ->setUsername($row['username'])
            ->setRole(Role::PELANGGAN);

        $kategori = (new KategoriPelanggan())
            ->setId($row['kategori_id'])
            ->setKategori($row['kategori']);

        return (new Pelanggan())
            ->setId($row['id'])
            ->setNama($row['nama'])
            ->setKontak($row['kontak'])
            ->setAkun($akun)
            ->setKategoriPelanggan($kategori)
            ->setPhoto($row['photo_id'])
            ->setPhotoIdentitas($row['photo_identitas']);
    }

}
