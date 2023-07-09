<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/Rute.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';

class RuteRepository extends BaseRepository
{
    private string $table = 'm_rute';

    public function get(int $total = 50, int $from = 0) : array
    {
        $query = "SELECT 
            r.*,
            d1.nama_kota as nama_kota_asal,
            d2.nama_kota as nama_kota_tujuan
            FROM {$this->getTable()} r
                JOIN m_daerah_operasional d1 ON d1.id = r.asal_id 
                JOIN m_daerah_operasional d2 ON d2.id = r.tujuan_id 
            ORDER BY nama_kota_asal
            LIMIT {$from}, {$total}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function findById(int $id) : ?Rute
    {
        $query = "SELECT 
            r.*,
            d1.nama_kota as nama_kota_asal,
            d2.nama_kota as nama_kota_tujuan
            FROM {$this->getTable()} r
                JOIN m_daerah_operasional d1 ON d1.id = r.asal_id 
                JOIN m_daerah_operasional d2 ON d2.id = r.tujuan_id 
            WHERE r.id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['id' => $id]);

        return ($stmt->rowCount()) ? $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC)) : null;
    }

    public function existsWithRelationship(Rute $rute, DaerahOperasional $asal, DaerahOperasional $tujuan): bool
    {
        $query = "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE id = :id AND asal_id = :asal AND tujuan_id = :tujuan) as 'exists'";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'id' => $rute->getId(),
            'asal' => $asal->getId(),
            'tujuan' => $tujuan->getId()
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function existsAsalAndTujuan(DaerahOperasional $asal, DaerahOperasional $tujuan) : bool
    {
        $query = "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE asal_id = :asal AND tujuan_id = :tujuan) as 'exists'";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'asal' => $asal->getId(),
            'tujuan' => $tujuan->getId()
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function getByAsalAndTujuanOrReversed(DaerahOperasional $asal, DaerahOperasional $tujuan) : null|Rute
    {
        $query = "SELECT 
            r.*,
            d1.nama_kota as nama_kota_asal,
            d2.nama_kota as nama_kota_tujuan
            FROM {$this->getTable()} r
            JOIN m_daerah_operasional d1 ON d1.id = r.asal_id 
            JOIN m_daerah_operasional d2 ON d2.id = r.tujuan_id 
            WHERE r.asal_id IN (:asal, :tujuan) AND r.tujuan_id IN (:asal, :tujuan)  ";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $found = $stmt->execute([
            'asal' => $asal->getId(),
            'tujuan' => $tujuan->getId()
        ]);

        return $found ? $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC)) : null;
    }

    public function update(Rute $rute) : false|Rute
    {
        if ($this->existsAsalAndTujuan($rute->getAsal(), $rute->getTujuan())) return false;

        $query = "UPDATE {$this->getTable()} SET asal_id = :asal, tujuan_id = :tujuan WHERE id = :id";
        $stmt = $this->getDatabaseConnection()->prepare($query);

        $stmt->execute([
            'id' => $rute->getId(),
            'asal' => $rute->getAsal()->getId(),
            'tujuan' => $rute->getTujuan()->getId()
        ]);

        return $rute;
    }

    public function save(Rute $rute) : false|Rute
    {
        if ($this->existsAsalAndTujuan($rute->getAsal(), $rute->getTujuan())) return false;

        $saved = $this->basicSave([
            'asal_id' => $rute->getAsal()->getId(),
            'tujuan_id' => $rute->getTujuan()->getId()
        ]);

        return $saved ? $rute->setId($saved) : false;
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $row, bool $withRelations = false) : Rute
    {
        $asal = (new DaerahOperasional())->setId($row['asal_id'])->setNamaKota($row['nama_kota_asal']);
        $tujuan = (new DaerahOperasional())->setId($row['tujuan_id'])->setNamaKota($row['nama_kota_tujuan']);

        return (new Rute())
            ->setId($row['id'])
            ->setAsal($asal)
            ->setTujuan($tujuan);
    }

}