<?php

require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../enums/Role.php';
require_once __DIR__ . '/BaseRepository.php';

class MobilRepository extends BaseRepository
{
    private string $table = 'mobil';

    public function save(Mobil $mobil) : bool
    {
        return $this->basicSave([
            'merk' => $mobil->getMerk(),
            'plat_nomor' => $mobil->getNomorPolisi(),
            'jumlah_kursi' => $mobil->getJumlahKursi(),
            'driver_id' => $mobil->getDriver()?->getId()
        ]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $query = "SELECT 
                m.*, u.id as user_id, u.username, u.kontak, u.nama_lengkap, u.level
            FROM {$this->getTable()} m
            LEFT JOIN users u 
            ON u.id = m.driver_id
            WHERE u.level = :role
            LIMIT {$from}, {$length}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'role' => Role::DRIVER->value
        ]);

        if($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mobil = $this->newEntity($row);
            $driver = new Akun();
            $driver
                ->setId($row['user_id'])
                ->setNamaLengkap($row['nama_lengkap'])
                ->setRole(Role::fromLabel($row['level']))
                ->setKontak($row['kontak'])
                ->setUsername($row['username']);

            $mobil->setDriver($driver);
            $result[] = $mobil;
        }

        return $result;
    }

    private function newEntity(array $row): Mobil
    {
        return (new Mobil())
            ->setId($row['id'])
            ->setMerk($row['merk'])
            ->setNomorPolisi($row['plat_nomor'])
            ->setJumlahKursi($row['jumlah_kursi'])
        ;
    }

    protected function getTable() : string
    {
        return $this->table;
    }
}