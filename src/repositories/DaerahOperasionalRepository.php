<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/DaerahOpersional.php';

class DaerahOperasionalRepository extends BaseRepository
{

    private string $table = "m_daerah_operasional";

    public function getTableName() : string
    {
        return $this->table;
    }
    public function save(DaerahOpersional $daerah) : bool
    {
        $query = $this->getDatabaseConnection()->prepare("INSERT INTO {$this->table} (nama_kota) VALUES (:nama_kota)");

        return $query->execute(['nama_kota' => $daerah->getNamaKota()]);
    }

    public function updateOrCreate(DaerahOpersional $daerahOpersional): bool
    {
        if ($this->exists($daerahOpersional)) return $this->update($daerahOpersional);

        return $this->save($daerahOpersional);
    }

    public function update(DaerahOpersional $daerahOpersional): bool
    {
        $query = "UPDATE {$this->getTable()} SET nama_kota = :nama_kota WHERE id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);

        return $stmt->execute([
            'id' => $daerahOpersional->getId(),
            'nama_kota' => $daerahOpersional->getNamaKota()
        ]);
    }

    public function delete(int|DaerahOpersional $daerahOpersional): void
    {
        $query = "DELETE FROM {$this->getTable()} WHERE id = :id";
        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['id' => is_int($daerahOpersional) ? $daerahOpersional : $daerahOpersional->getId() ]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from, 'nama_kota', 'ASC');

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