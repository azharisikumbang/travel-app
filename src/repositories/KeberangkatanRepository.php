<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/RuteHarian.php';
require_once __DIR__ . '/../entities/DaerahOpersional.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';
require_once __DIR__ . '/../entities/Mobil.php';

class KeberangkatanRepository extends BaseRepository
{
    private string $table = 'rute_harian';

    public function get(int $total = 50, int $from = 0) : array
    {
        $query = "SELECT 
                r.*,
                d1.nama_kota as do_asal_kota,
                d2.nama_kota as do_tujuan_kota,
                m.merk as m_merk,
                m.jumlah_kursi as m_jumlah_kursi,
                m.plat_nomor as m_plat_nomor,
                j.jam as j_jam,
                j.alias as j_alias,
                u.id as driver_id,
                u.nama_lengkap as u_nama_lengkap,
                u.kontak as u_kontak
            FROM rute_harian r
            LEFT JOIN daerah_operasional d1 ON d1.id = r.asal_id
            LEFT JOIN daerah_operasional d2 ON d2.id = r.tujuan_id
            LEFT JOIN mobil m ON m.id = r.mobil_id
            LEFT JOIN jadwal_keberangkatan j ON j.id = r.jam_keberangkatan_id
            LEFT JOIN users u ON m.driver_id = u.id
            ORDER BY do_asal_kota ASC
        ";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row, true);

        return $result;

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

    public function isAsalAndTujuanExists(int|DaerahOpersional $asal, int|DaerahOpersional $tujuan) : bool
    {
        $query = "SELECT EXISTS(SELECT id FROM rute_harian WHERE asal_id = :asal AND tujuan_id = :tujuan) as 'exists'";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'asal' => is_int($asal) ? $asal : $asal->getId(),
            'tujuan' => is_int($tujuan) ? $tujuan : $tujuan->getId()
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function save(RuteHarian $rute): bool
    {
        return $this->basicSave([
            'asal_id' => $rute->getAsal()->getId(),
            'tujuan_id' => $rute->getTujuan()->getId(),
            'mobil_id' => $rute->getMobil()?->getId(),
            'jam_keberangkatan_id' => $rute->getJamKeberangkatan()?->getId()
        ]);
    }

    private function newEntity(array $row, bool $withRelations = false) : RuteHarian
    {
        $asal = (new DaerahOpersional())
            ->setId($row['asal_id'])
            ->setNamaKota($row['do_asal_kota']);

        $tujuan = (new DaerahOpersional())
            ->setId($row['tujuan_id'])
            ->setNamaKota($row['do_tujuan_kota']);

        $driver = (is_null($row['mobil_id']))
            ? null
            : (new Akun())
                ->setId($row['driver_id'])
                ->setNamaLengkap($row['u_nama_lengkap'])
                ->setKontak($row['u_kontak']);

        $mobil = (is_null($row['mobil_id']))
            ? null
            : (new Mobil())
                ->setId($row['mobil_id'])
                ->setMerk($row['m_merk'])
                ->setJumlahKursi($row['m_jumlah_kursi'])
                ->setNomorPolisi($row['m_plat_nomor'])
                ->setDriver($driver);

        $jam = (is_null($row['mobil_id']))
            ? null
            : (new JamKeberangkatan())
                ->setId($row['jam_keberangkatan_id'])
                ->setAlias($row['j_alias'])
                ->setJam($row['j_jam']);

        return (new RuteHarian())
            ->setId($row['id'])
            ->setAsal($asal)
            ->setTujuan($tujuan)
            ->setMobil($mobil)
            ->setJamKeberangkatan($jam)
            ->setLastUpdated(date_create($row['last_updated']));
    }

    protected function getTable(): string
    {
        return $this->table;
    }

}