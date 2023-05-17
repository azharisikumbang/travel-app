<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';

class JamKeberangkatanRepository extends BaseRepository
{
    protected string $table = "jadwal_keberangkatan";

    protected function getTable(): string
    {
        return $this->table;
    }

    public function save(JamKeberangkatan $jamKeberangkatan) : bool
    {
        $bind = [
            'jam' => $jamKeberangkatan->getJam(),
            'alias' => $jamKeberangkatan->getAlias()
        ];

        return $this->basicSave($bind);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) {
            $jamKeberangkatan = new JamKeberangkatan();
            $jamKeberangkatan
                ->setId($row['id'])
                ->setJam($row['jam'])
                ->setAlias($row['alias']);

            $result[] = $jamKeberangkatan;
        }

        return $result;
    }

}
