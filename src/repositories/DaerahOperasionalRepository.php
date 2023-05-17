<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/DaerahOpersional.php';

class DaerahOperasionalRepository extends BaseRepository
{

    private string $table = "daerah_operasional";

    public function getTableName() : string
    {
        return $this->table;
    }
    public function save(DaerahOpersional $daerah) : bool
    {
        $query = $this->getDatabaseConnection()->prepare("INSERT INTO {$this->table} (nama_kota) VALUES (:nama_kota)");

        return $query->execute(['nama_kota' => $daerah->getNamaKota()]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function findById(int $id) : null|DaerahOpersional
    {
        $data = $this->basicFindById($id);

        if(!$data) return null;

        return $this->newEntity($data);
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $data) : DaerahOpersional
    {
        return (new DaerahOpersional())
            ->setId($data['id'])
            ->setNamaKota($data['nama_kota']);
    }
}