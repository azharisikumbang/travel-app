<?php

require_once __DIR__ . '/BaseRepository.php';

class TarifRepository extends BaseRepository
{
    protected string $table = 'tarif';

    public function __construct(
        private readonly TipePenumpangRepository $tipePenumpangRepository,
        private readonly DaerahOperasionalRepository $daerahOperasionalRepository
    ) { }

    public function save(Tarif $tarif) : bool
    {
        if(!($tarif->getTipePenumpang() instanceof TipePenumpang)) return false;
        if(!($tarif->getAsal() instanceof DaerahOpersional)) return false;
        if(!($tarif->getTujuan() instanceof DaerahOpersional)) return false;

        return $this->basicSave([
            'kota_asal' => $tarif->getAsal()->getId(),
            'kota_tujuan' => $tarif->getTujuan()->getId(),
            'tipe_penumpang' => $tarif->getTipePenumpang()->getId(),
            'tarif' => $tarif->getTarif()
        ]);
    }

    public function findById(int $id) : null|Tarif
    {
        $query = "SELECT
            t.id as tarif_id,
            t.kota_asal as kota_asal_id,
            t.kota_tujuan as kota_tujuan_id,
            t.tipe_penumpang as tipe_penumpang_id,
            t.tarif,
            d.nama_kota as kota_asal,
            o.nama_kota as kota_tujuan,
            tp.tipe_penumpang as tipe_penumpang
        FROM tarif t
        JOIN daerah_operasional d on t.kota_asal = d.id
        JOIN daerah_operasional o on o.id = t.kota_tujuan
        JOIN tipe_penumpang tp on t.tipe_penumpang = tp.id
        WHERE t.id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return $this->newEntity($data);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $query = "SELECT
            t.id as tarif_id,
            t.kota_asal as kota_asal_id,
            t.kota_tujuan as kota_tujuan_id,
            t.tipe_penumpang as tipe_penumpang_id,
            t.tarif,
            d.nama_kota as kota_asal,
            o.nama_kota as kota_tujuan,
            tp.tipe_penumpang as tipe_penumpang
        FROM tarif t
        JOIN daerah_operasional d on t.kota_asal = d.id
        JOIN daerah_operasional o on o.id = t.kota_tujuan
        JOIN tipe_penumpang tp on t.tipe_penumpang = tp.id
        ORDER BY tipe_penumpang ASC
        LIMIT {$from}, {$length}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function cariTarif(int $asal, int $tujuan, int $kategori) 
    {
        $query = "SELECT
            t.id as tarif_id,
            t.kota_asal as kota_asal_id,
            t.kota_tujuan as kota_tujuan_id,
            t.tipe_penumpang as tipe_penumpang_id,
            t.tarif,
            d.nama_kota as kota_asal,
            o.nama_kota as kota_tujuan,
            tp.tipe_penumpang as tipe_penumpang
        FROM tarif t
        JOIN daerah_operasional d on t.kota_asal = d.id
        JOIN daerah_operasional o on o.id = t.kota_tujuan
        JOIN tipe_penumpang tp on t.tipe_penumpang = tp.id
        WHERE t.kota_asal = :asal AND t.kota_tujuan = :tujuan AND t.tipe_penumpang = :kategori";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'asal' => $asal,
            'tujuan' => $tujuan,
            'kategori' => $kategori
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return $this->newEntity($data);
    }

    public function listRuteBerdasarkanKota(): false|array
    {
        $query = "SELECT 
            t.kota_asal, 
            d1.nama_kota as nama_kota_asal,
            t.kota_tujuan,
            d2.nama_kota as nama_kota_tujuan
        FROM `tarif` t
        JOIN daerah_operasional d1 ON d1.id = t.kota_asal
        JOIN daerah_operasional d2 ON d2.id = t.kota_tujuan
        GROUP BY nama_kota_asal, nama_kota_tujuan
        ORDER BY nama_kota_asal ASC";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $row) : Tarif
    {
        $asal = new DaerahOpersional();
        $asal
            ->setId($row['kota_asal_id'])
            ->setNamaKota($row['kota_asal']);

        $tujuan = new DaerahOpersional();
        $tujuan
            ->setId($row['kota_tujuan_id'])
            ->setNamaKota($row['kota_tujuan']);

        $tipe = new TipePenumpang();
        $tipe
            ->setId($row['tipe_penumpang_id'])
            ->setTipePenumpang($row['tipe_penumpang']);

        $tarif = new Tarif();
        $tarif
            ->setId($row['tarif_id'])
            ->setTarif($row['tarif'])
            ->setAsal($asal)
            ->setTujuan($tujuan)
            ->setTipePenumpang($tipe);

        return $tarif;
    }
}
