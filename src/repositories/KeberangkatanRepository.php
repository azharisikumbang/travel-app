<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/Keberangkatan.php';
require_once __DIR__ . '/../entities/Rute.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';
require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Driver.php';

class KeberangkatanRepository extends BaseRepository
{
    private string $table = 'm_keberangkatan';

    public function get(bool $withRelations = false) : array
    {
        if ($withRelations) return $this->getWithRelations();



    }

    public function getWithRelations()
    {
        $query = "SELECT
                k.*,
                mr.asal_id as r_asal_id,
                mr.tujuan_id as r_tujuan_id,
                mdo1.nama_kota as r_asal_nama_kota,
                mdo2.nama_kota as r_tujuan_nama_kota,
                mjk.jam as jk_jam,
                mjk.alias as jk_alias,
                mm.merk as m_merk,
                mm.plat_nomor as m_plat_nomor,
                mm.supir_id as m_supir_id,
                ms.nama as s_nama
            FROM m_keberangkatan k
            LEFT JOIN m_jadwal_keberangkatan mjk on k.jam_keberangkatan_id = mjk.id
            LEFT JOIN m_mobil mm on k.mobil_id = mm.id
            LEFT JOIN m_rute mr on k.rute_id = mr.id
            LEFT JOIN m_supir ms on mm.supir_id = ms.id
            LEFT JOIN m_daerah_operasional mdo1 on mr.asal_id = mdo1.id
            LEFT JOIN m_daerah_operasional mdo2 on mr.tujuan_id = mdo2.id
            ORDER BY s_nama, m_merk, r_asal_nama_kota, r_tujuan_nama_kota";

        return $this->getByQuery($query, [], true);
    }

    public function getWhere(array $where) : ?RuteHarian
    {
        return $this->newEntity([], true);
    }

    public function updateMobilAndJam(RuteHarian $rute, Mobil $mobil, JamKeberangkatan $jam): bool
    {
        $query = "UPDATE {$this->getTable()} SET mobil_id = :mobil_id, jam_keberangkatan_id = :jam_keberangkatan_id WHERE id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        return $stmt->execute([
            'mobil_id' => $mobil->getId(),
            'jam_keberangkatan_id' => $jam->getId(),
            'id' => $rute->getId()
        ]);
    }

    public function isAsalAndTujuanExists(int|DaerahOperasional $asal, int|DaerahOperasional $tujuan) : bool
    {
        $query = "SELECT EXISTS(SELECT id FROM rute_harian WHERE asal_id = :asal AND tujuan_id = :tujuan) as 'exists'";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'asal' => is_int($asal) ? $asal : $asal->getId(),
            'tujuan' => is_int($tujuan) ? $tujuan : $tujuan->getId()
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function save(Keberangkatan $keberangkatan): bool
    {
        return $this->basicSave([
            'rute_id' => $keberangkatan->getRute()->getId(),
            'tujuan_id' => $keberangkatan->getTujuan()->getId(),
            'mobil_id' => $keberangkatan->getMobil()?->getId(),
            'jam_keberangkatan_id' => $keberangkatan->getJamKeberangkatan()?->getId()
        ]);
    }

    protected function newEntity(array $row, bool $withRelations = false) : Keberangkatan
    {
        if($withRelations) return $this->newEntityWithRelations($row);

        $keberangkatan = new Keberangkatan();
        $keberangkatan->setId($row['id']);
        $keberangkatan->setRute($row['rute_id']);
        $keberangkatan->setMobil($row['mobil_id']);
        $keberangkatan->setJamKeberangkatan($row['jam_keberangkatan_id']);
        $keberangkatan->setLastUpdated(date_create($row['last_updated']));

        return $keberangkatan;
    }

    protected function newEntityWithRelations(array $row)
    {
        $asal = new DaerahOperasional();
        $asal->setId($row['r_asal_id']);
        $asal->setNamaKota($row['r_asal_nama_kota']);

        $tujuan = new DaerahOperasional();
        $tujuan->setId($row['r_tujuan_id']);
        $tujuan->setNamaKota($row['r_tujuan_nama_kota']);

        $rute = new Rute();
        $rute->setId($row['rute_id']);
        $rute->setAsal($asal);
        $rute->setTujuan($tujuan);

        $mobil = null;
        if (!is_null($row['mobil_id']))
        {
            $driver = new Driver();
            $driver->setId($row['m_supir_id']);
            $driver->setNama($row['s_nama']);

            $mobil = new Mobil();
            $mobil->setId($row['mobil_id']);
            $mobil->setMerk($row['m_merk']);
            $mobil->setPlatNomor($row['m_plat_nomor']);
            $mobil->setDriver($driver);
        }

        $jamKeberangkatan = null;
        if(!is_null($row['jam_keberangkatan_id'])) {
            $jamKeberangkatan = new JamKeberangkatan();
            $jamKeberangkatan->setId($row['jam_keberangkatan_id']);
            $jamKeberangkatan->setJam($row['jk_jam']);
            $jamKeberangkatan->setAlias($row['jk_alias']);
        }

        $keberangkatan = new Keberangkatan();
        $keberangkatan->setId($row['id']);
        $keberangkatan->setRute($rute);
        $keberangkatan->setMobil($mobil);
        $keberangkatan->setJamKeberangkatan($jamKeberangkatan);
        $keberangkatan->setLastUpdated(date_create($row['last_updated']));

        return $keberangkatan;
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    public function update(Mobil $mobil, JamKeberangkatan $jamKeberangkatan, array $existsRute) : bool
    {
        $dbh = $this->getDatabaseConnection();

        $query = $dbh->prepare("DELETE FROM {$this->getTable()} WHERE mobil_id = :mobil");

        try {

            $dbh->beginTransaction();
            $query->execute(['mobil' => $mobil->getId()]);

            foreach ($existsRute as $item) {
                if(!($item instanceof Rute)) continue;

                $this->basicSave([
                    'mobil_id' => $mobil->getId(),
                    'jam_keberangkatan_id' => $jamKeberangkatan->getId(),
                    'rute_id' => $item->getId()
                ]);
            }

            $dbh->commit();

        } catch (Exception $e) {
            $dbh->rollBack();

            die($e->getMessage());
        }

        return true;
    }

    public function resetByMobil(int|Mobil $mobil) : bool
    {
        $stmt = $this->getDatabaseConnection()->prepare("DELETE FROM {$this->getTable()} WHERE mobil_id = :mobil");

        return $stmt->execute([
            'mobil' => is_int($mobil) ? $mobil : $mobil->getId()
        ]);
    }

}