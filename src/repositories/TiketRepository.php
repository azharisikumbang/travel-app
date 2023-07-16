<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../entities/Tiket.php';
require_once __DIR__ . '/../entities/Rute.php';
require_once __DIR__ . '/../entities/KategoriPelanggan.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';

class TiketRepository extends BaseRepository
{
    protected string $table = 'm_tiket';

    public function get(int $length = 10, int $from = 0): array
    {
        return $this->getByQuery(
            "SELECT t.*, 
                    kp.kategori as kp_kategori,
                    r.asal_id as r_asal_id,
                    r.tujuan_id as r_tujuan_id,
                    mdo1.nama_kota as r_asal_nama_kota,
                    mdo1.provinsi as r_asal_provinsi,
                    mdo2.nama_kota as r_tujuan_nama_kota,
                    mdo2.provinsi as r_tujuan_provinsi
                FROM {$this->getTable()} t 
                JOIN m_rute r ON r.id = t.rute_id
                JOIN m_kategori_pelanggan kp ON kp.id = t.kategori_penumpang_id
                JOIN m_daerah_operasional mdo1 ON r.asal_id = mdo1.id
                JOIN m_daerah_operasional mdo2 ON r.tujuan_id = mdo2.id
                ORDER BY kp_kategori, r_asal_nama_kota, r_tujuan_nama_kota
                LIMIT {$from}, {$length}
            "
        );
    }

    public function updateOrCreate(Tiket $tiket) : false|Tiket
    {
        if ($this->exists($tiket)) return $this->update($tiket);

        return $this->save($tiket);
    }

    public function save(Tiket $tiket) : false|Tiket
    {
        if ($this->isRuteAndKategoriExists($tiket->getRute(), $tiket->getKategoriPelanggan())) return false;

        $saved = $this->basicSave([
            'rute_id' => $tiket->getRute()->getId(),
            'kategori_penumpang_id' => $tiket->getKategoriPelanggan()->getId(),
            'tarif' => $tiket->getTarif()
        ]);

        return $saved ? $tiket->setId($saved) : false;
    }

    public function update(Tiket $tiket): false|Tiket
    {
        $query = "UPDATE {$this->getTable()} SET rute_id = :rute_id, kategori_penumpang_id = :kategori_penumpang_id, tarif = :tarif WHERE id = :id";
        $stmt = $this->getDatabaseConnection()->prepare($query);

        $updated = $stmt->execute([
            'rute_id' => $tiket->getRute()->getId(),
            'kategori_penumpang_id' => $tiket->getKategoriPelanggan()->getId(),
            'tarif' => $tiket->getTarif(),
            'id' => $tiket->getId()
        ]);

        return $updated ? $tiket : false;
    }

    public function isRuteAndKategoriExists(Rute $rute, KategoriPelanggan $kategoriPelanggan): bool
    {
        $query = "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE rute_id = :rute AND kategori_penumpang_id = :kategori_penumpang) as 'exists'";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'rute' => $rute->getId(),
            'kategori_penumpang' => $kategoriPelanggan->getId()
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function getByRuteAndKategori(Rute $rute, KategoriPelanggan $kategoriPelanggan): ?Tiket
    {
        $query = "SELECT t.*, 
                    kp.kategori as kp_kategori,
                    r.asal_id as r_asal_id,
                    r.tujuan_id as r_tujuan_id,
                    mdo1.nama_kota as r_asal_nama_kota,
                    mdo1.provinsi as r_asal_provinsi,
                    mdo2.nama_kota as r_tujuan_nama_kota,
                    mdo2.provinsi as r_tujuan_provinsi
                FROM {$this->getTable()} t 
                JOIN m_rute r ON r.id = t.rute_id
                JOIN m_kategori_pelanggan kp ON kp.id = t.kategori_penumpang_id
                JOIN m_daerah_operasional mdo1 ON r.asal_id = mdo1.id
                JOIN m_daerah_operasional mdo2 ON r.tujuan_id = mdo2.id
                WHERE t.rute_id = :rute AND t.kategori_penumpang_id = :kategori_penumpang";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $found = $stmt->execute([
            'rute' => $rute->getId(),
            'kategori_penumpang' => $kategoriPelanggan->getId()
        ]);

        return $found ? $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC)) : null;
    }

    public function findById(int $id) : null|Tiket
    {
        $query = "SELECT t.*, 
                    kp.kategori as kp_kategori,
                    r.asal_id as r_asal_id,
                    r.tujuan_id as r_tujuan_id,
                    mdo1.nama_kota as r_asal_nama_kota,
                    mdo1.provinsi as r_asal_provinsi,
                    mdo2.nama_kota as r_tujuan_nama_kota,
                    mdo2.provinsi as r_tujuan_provinsi
                FROM {$this->getTable()} t 
                JOIN m_rute r ON r.id = t.rute_id
                JOIN m_kategori_pelanggan kp ON kp.id = t.kategori_penumpang_id
                JOIN m_daerah_operasional mdo1 ON r.asal_id = mdo1.id
                JOIN m_daerah_operasional mdo2 ON r.tujuan_id = mdo2.id
                WHERE t.id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);

        return $stmt->execute(['id' => $id]) ? $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC)) : null;
    }

    protected function newEntity(array $row, bool $withRelations = false) : Tiket
    {
        $asal = new DaerahOperasional();
        $asal->setId($row['r_asal_id']);
        $asal->setNamaKota($row['r_asal_nama_kota']);
        $asal->setProvinsi(Provinsi::fromValue($row['r_asal_provinsi']));

        $tujuan = new DaerahOperasional();
        $tujuan->setId($row['r_tujuan_id']);
        $tujuan->setNamaKota($row['r_tujuan_nama_kota']);
        $tujuan->setProvinsi(Provinsi::fromValue($row['r_tujuan_provinsi']));

        $rute = new Rute();
        $rute->setId($row['rute_id']);
        $rute->setAsal($asal);
        $rute->setTujuan($tujuan);

        $kategori = new KategoriPelanggan();
        $kategori->setId($row['kategori_penumpang_id']);
        $kategori->setKategori($row['kp_kategori']);

        $tiket = new Tiket();
        $tiket->setId($row['id']);
        $tiket->setRute($rute);
        $tiket->setTarif($row['tarif']);
        $tiket->setKategoriPelanggan($kategori);

        return $tiket;
    }

    protected function getTable(): string
    {
        return $this->table;
    }
}
