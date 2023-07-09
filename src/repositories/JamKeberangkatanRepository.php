<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';

class JamKeberangkatanRepository extends BaseRepository
{
    protected string $table = "m_jadwal_keberangkatan";

    protected function getTable(): string
    {
        return $this->table;
    }

    public function updateOrCreate(JamKeberangkatan $jamKeberangkatan) : false|JamKeberangkatan
    {
        if ($this->exists($jamKeberangkatan)) return $this->update($jamKeberangkatan);

        return $this->save($jamKeberangkatan);
    }

    public function save(JamKeberangkatan $jamKeberangkatan): false|JamKeberangkatan
    {
        $saved = $this->basicSave([
            'jam' => $jamKeberangkatan->getJam(),
            'alias' => $jamKeberangkatan->getAlias()
        ]);

        return $saved ? $jamKeberangkatan->setId($saved) : false;
    }

    public function update(JamKeberangkatan $jamKeberangkatan) : false|JamKeberangkatan
    {
        $query = "UPDATE {$this->getTable()} SET jam = :jam, alias = :alias WHERE id = :id";
        $stmt = $this->getDatabaseConnection()->prepare($query);
        $updated = $stmt->execute([
            'id' => $jamKeberangkatan->getId(),
            'jam' => $jamKeberangkatan->getJam(),
            'alias' => $jamKeberangkatan->getAlias()
        ]);

        return $updated ? $jamKeberangkatan : false;
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from, 'jam', 'asc');

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function findById(int $id) : ?JamKeberangkatan
    {
        $data = $this->basicFindById($id);

        if(!$data) return null;

        return $this->newEntity($data);
    }

    protected function newEntity(array $row, bool $withRelations = false) : JamKeberangkatan
    {
        return (new JamKeberangkatan())
            ->setId($row['id'])
            ->setJam($row['jam'])
            ->setAlias($row['alias']);
    }
}
