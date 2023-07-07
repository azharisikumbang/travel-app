<?php

require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../enums/Role.php';
require_once __DIR__ . '/BaseRepository.php';

class MobilRepository extends BaseRepository
{
    private string $table = 'm_mobil';

    public function updateOrCreate(Mobil $mobil) : false|Mobil
    {
        if($this->exists($mobil)) return $this->update($mobil);

        return $this->save($mobil);
    }

    public function save(Mobil $mobil) : false|Mobil
    {
        $saved = $this->basicSave([
            'merk' => $mobil->getMerk(),
            'plat_nomor' => $mobil->getPlatNomor(),
            'jumlah_kursi' => $mobil->getJumlahKursi(),
            'supir_id' => $mobil->getDriver()?->getId()
        ]);

        return $saved ? $mobil->setId($saved) : false;
    }

    public function update(Mobil $mobil): false|Mobil
    {
        $query = "UPDATE {$this->getTable()} 
            SET merk = :merk, jumlah_kursi = :jumlah_kursi, plat_nomor = :plat_nomor, supir_id = :supir 
            WHERE id = :id" ;

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $updated = $stmt->execute([
            'id' => $mobil->getId(),
            'merk' => $mobil->getMerk(),
            'plat_nomor' => $mobil->getPlatNomor(),
            'jumlah_kursi' => $mobil->getJumlahKursi(),
            'supir' => $mobil->getDriver()?->getId()
        ]);

        return $updated ? $mobil : false;
    }

    public function get(DriverRepository $driverRepository, int $length = 10, int $from = 0) : array
    {
        $query = "SELECT m.*, s.nama as supir_nama, s.kontak as supir_kontak
            FROM {$this->getTable()} m
            JOIN {$driverRepository->getTable()} s ON s.id = m.supir_id
            LIMIT {$from}, {$length}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    protected function newEntity(array $row): Mobil
    {
        $driver = new Driver();
        $driver->setId($row['supir_id']);
        $driver->setNama($row['supir_nama']);
        $driver->setKontak($row['supir_kontak']);
        $driver->setAkun(null);

        return (new Mobil())
            ->setId($row['id'])
            ->setMerk($row['merk'])
            ->setPlatNomor($row['plat_nomor'])
            ->setJumlahKursi($row['jumlah_kursi'])
            ->setDriver($driver)
        ;
    }

    protected function getTable() : string
    {
        return $this->table;
    }
}