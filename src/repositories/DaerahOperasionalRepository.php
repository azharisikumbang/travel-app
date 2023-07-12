<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';

class DaerahOperasionalRepository extends BaseRepository
{

    private string $table = "m_daerah_operasional";

    public function save(DaerahOperasional $daerah) : bool
    {
        $query = $this->getDatabaseConnection()->prepare("INSERT INTO {$this->table} (nama_kota, provinsi) VALUES (:nama_kota, :provinsi)");

        return $query->execute([
            'nama_kota' => $daerah->getNamaKota(),
            'provinsi' => $daerah->getProvinsi()->value
        ]);
    }

    public function updateOrCreate(DaerahOperasional $daerahOpersional): bool
    {
        if ($this->exists($daerahOpersional)) return $this->update($daerahOpersional);

        return $this->save($daerahOpersional);
    }

    public function update(DaerahOperasional $daerahOpersional): bool
    {
        $query = "UPDATE {$this->getTable()} SET nama_kota = :nama_kota, provinsi = :provinsi WHERE id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);

        return $stmt->execute([
            'id' => $daerahOpersional->getId(),
            'nama_kota' => $daerahOpersional->getNamaKota(),
            'provinsi' => $daerahOpersional->getProvinsi()->value
        ]);
    }

    public function delete(int|DaerahOperasional $daerahOpersional): void
    {
        $query = "DELETE FROM {$this->getTable()} WHERE id = :id";
        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['id' => is_int($daerahOpersional) ? $daerahOpersional : $daerahOpersional->getId() ]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from, 'provinsi', 'ASC');

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function findById(int $id) : null|DaerahOperasional
    {
        $data = $this->basicFindById($id);

        if(!$data) return null;

        return $this->newEntity($data);
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $data, bool $withRelations = false) : DaerahOperasional
    {
        return (new DaerahOperasional())
            ->setId($data['id'])
            ->setNamaKota($data['nama_kota'])
            ->setProvinsi(Provinsi::fromValue($data['provinsi']))
            ;
    }
}