<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/KategoriPelanggan.php';

class KategoriPelangganRepository extends BaseRepository
{
    protected string $table = "m_kategori_pelanggan";

    public function updateOrCreate(KategoriPelanggan $kategori) : false|KategoriPelanggan
    {
        if ($this->exists($kategori)) return $this->update($kategori);

        return $this->save($kategori);
    }

    public function save(KategoriPelanggan $kategori): false|KategoriPelanggan
    {
        $saved = $this->basicSave([
            'kategori' => $kategori->getKategori()
        ]);

        return $saved ? $kategori->setId($saved) : false;
    }

    public function update(KategoriPelanggan $kategori) : false|KategoriPelanggan
    {
        $query = "UPDATE {$this->getTable()} SET kategori = :kategori WHERE id = :id";
        $stmt = $this->getDatabaseConnection()->prepare($query);
        $updated = $stmt->execute([
            'id' => $kategori->getId(),
            'kategori' => $kategori->getKategori()
        ]);

        return $updated ? $kategori : false;
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from, 'kategori', 'asc');

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function findById(int $id) : ?KategoriPelanggan
    {
        $data = $this->basicFindById($id);

        if(!$data) return null;

        return $this->newEntity($data);
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $row, bool $withRelations = false): KategoriPelanggan
    {
        return (new KategoriPelanggan())
            ->setId($row['id'])
            ->setKategori($row['kategori']);
    }
}
