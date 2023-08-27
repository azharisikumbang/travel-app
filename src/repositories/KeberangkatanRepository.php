<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/Keberangkatan.php';
require_once __DIR__ . '/../entities/Rute.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';
require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Driver.php';
require_once __DIR__ . '/../enums/Provinsi.php';

class KeberangkatanRepository extends BaseRepository
{
    private string $table = 'm_keberangkatan';

    public function get(bool $withRelations = false) : array
    {
        if ($withRelations) return $this->getWithRelations();
    }

    public function findByJamKeberangkatan(int|JamKeberangkatan $jam)
    {
        $query = "SELECT
                k.*,
                mjk.jam as jk_jam,
                mjk.alias as jk_alias,
                mm.merk as m_merk,
                mm.plat_nomor as m_plat_nomor,
                mm.jumlah_kursi as m_jumlah_kursi,
                mm.supir_id as m_supir_id,
                ms.nama as s_nama
            FROM m_keberangkatan k
            LEFT JOIN m_jadwal_keberangkatan mjk on k.jam_keberangkatan_id = mjk.id
            LEFT JOIN m_mobil mm on k.mobil_id = mm.id
            LEFT JOIN m_supir ms on mm.supir_id = ms.id
            WHERE jam_keberangkatan_id = :jam_keberangkatan_id
            ORDER BY s_nama, m_merk, k.provinsi_id
            ";

        return $this->getByQuery($query, ['jam_keberangkatan_id' => is_int($jam) ? $jam : $jam->getId()], true);
    }

    public function getWithRelations()
    {
        $query = "SELECT
                k.*,
                mjk.jam as jk_jam,
                mjk.alias as jk_alias,
                mm.merk as m_merk,
                mm.plat_nomor as m_plat_nomor,
                mm.jumlah_kursi as m_jumlah_kursi,
                mm.supir_id as m_supir_id,
                ms.nama as s_nama
            FROM m_keberangkatan k
            LEFT JOIN m_jadwal_keberangkatan mjk on k.jam_keberangkatan_id = mjk.id
            LEFT JOIN m_mobil mm on k.mobil_id = mm.id
            LEFT JOIN m_supir ms on mm.supir_id = ms.id
            ORDER BY s_nama, m_merk, k.provinsi_id";

        return $this->getByQuery($query, [], true);
    }

    public function getWhere(array $where) : array
    {

        $query = "SELECT
                k.*,
                mjk.jam as jk_jam,
                mjk.alias as jk_alias,
                mm.merk as m_merk,
                mm.plat_nomor as m_plat_nomor,
                mm.jumlah_kursi as m_jumlah_kursi,
                mm.supir_id as m_supir_id,
                ms.nama as s_nama
            FROM m_keberangkatan k
            LEFT JOIN m_jadwal_keberangkatan mjk on k.jam_keberangkatan_id = mjk.id
            LEFT JOIN m_mobil mm on k.mobil_id = mm.id
            LEFT JOIN m_supir ms on mm.supir_id = ms.id
            WHERE k.provinsi_id = :provinsi_id
            ORDER BY s_nama, m_merk, k.provinsi_id";

        return $this->getByQuery($query, $where, true);
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

    public function existsByMobil(Mobil $mobil) : bool
    {
        $query = "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE mobil_id = :mobil) as 'exists'";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'mobil' => $mobil->getId()
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function save(Keberangkatan $keberangkatan): false|Keberangkatan
    {
        $saved = $this->basicSave([
            'provinsi_id' => $keberangkatan->getProvinsi()?->value,
            'mobil_id' => $keberangkatan->getMobil()?->getId(),
            'jam_keberangkatan_id' => $keberangkatan->getJamKeberangkatan()?->getId()
        ]);

        return $saved ? $keberangkatan->setId($saved) : false;
    }

    public function update(Keberangkatan $keberangkatan) : false|Keberangkatan
    {
        $query = "UPDATE {$this->getTable()}
            SET provinsi_id = :provinsi, jam_keberangkatan_id = jam_keberangkatan
            WHERE mobil_id = :mobil
        ";

        $stmt = $this->getDatabaseConnection()->prepare($query);

        $updated = $stmt = $stmt->execute([
            'provinsi_id' => $keberangkatan->getProvinsi()->value,
            'jam_keberangkatan_id' => $keberangkatan->getJamKeberangkatan()->getId(),
            'mobil_id' => $keberangkatan->getMobil()->getId()
        ]);

        return $updated ? $keberangkatan : false;
    }

    public function updateOrCreate(Keberangkatan $keberangkatan): false|Keberangkatan
    {
        if ($this->existsByMobil($keberangkatan->getMobil())) return $this->update($keberangkatan);

        return $this->save($keberangkatan);
    }

    protected function newEntity(array $row, bool $withRelations = false) : Keberangkatan
    {
        if($withRelations) return $this->newEntityWithRelations($row);

        $keberangkatan = new Keberangkatan();
        $keberangkatan->setId($row['id']);
        $keberangkatan->setProvinsi(Provinsi::fromValue($row['provinsi_id']));
        $keberangkatan->setMobil($row['mobil_id']);
        $keberangkatan->setJamKeberangkatan($row['jam_keberangkatan_id']);
        $keberangkatan->setLastUpdated(date_create($row['last_updated']));

        return $keberangkatan;
    }

    protected function newEntityWithRelations(array $row)
    {
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
            $mobil->setJumlahKursi($row['m_jumlah_kursi']);
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
        $keberangkatan->setProvinsi(Provinsi::fromValue($row['provinsi_id']));
        $keberangkatan->setMobil($mobil);
        $keberangkatan->setJamKeberangkatan($jamKeberangkatan);
        $keberangkatan->setLastUpdated(date_create($row['last_updated']));

        return $keberangkatan;
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    public function resetByMobil(int|Mobil $mobil) : bool
    {
        $stmt = $this->getDatabaseConnection()->prepare("DELETE FROM {$this->getTable()} WHERE mobil_id = :mobil");

        return $stmt->execute([
            'mobil' => is_int($mobil) ? $mobil : $mobil->getId()
        ]);
    }

}