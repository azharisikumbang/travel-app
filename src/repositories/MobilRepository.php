<?php

require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/BaseRepository.php';

class MobilRepository extends BaseRepository
{
    private string $table = 'mobil';

    public function save(Mobil $mobil) : bool
    {
        return $this->basicSave([
            'merk' => $mobil->getMerk(),
            'plat_nomor' => $mobil->getNomorPolisi(),
            'jumlah_kursi' => $mobil->getJumlahKursi()
        ]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) {
            $mobil = new Mobil();
            $mobil
                ->setId($row['id'])
                ->setMerk($row['merk'])
                ->setNomorPolisi($row['plat_nomor'])
                ->setJumlahKursi($row['jumlah_kursi'])
            ;

            $result[] = $mobil;
        }

        return $result;
    }

    protected function getTable() : string
    {
        return $this->table;
    }
}