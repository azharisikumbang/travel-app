<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/AkunRepository.php';
require_once __DIR__ . '/../entities/Driver.php';

class DriverRepository extends BaseRepository
{
    private string $table = 'm_supir';

    public function get(AkunRepository $akunRepository, int $total = 50, int $from = 0) : array
    {
        $query = "SELECT 
            d.*,
            a.username as akun_username,
            a.role as akun_role
            FROM {$this->getTable()} d
                JOIN {$akunRepository->getTable()} a ON a.id = d.akun_id 
            ORDER BY d.nama
            LIMIT {$from}, {$total}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function save(Driver $driver) : false|Driver
    {
        $saved = $this->basicSave([
            'nama' => $driver->getNama(),
            'kontak' => $driver->getKontak(),
            'akun_id' => $driver->getAkun()->getId(),
            'photo_id' => $driver->getPhoto()
        ]);

        return $saved ? $driver->setId($saved) : false;
    }

    public function update(Driver $driver) : false|Driver
    {
        $query = "UPDATE {$this->getTable()} SET nama = :nama, kontak = :kontak WHERE id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $updated = $stmt->execute([
            'nama' => $driver->getNama(),
            'kontak' => $driver->getKontak(),
            'id' => $driver->getId()
        ]);

        return $updated ? $driver : false;
    }

    public function findById(int $id, bool $withRelations = false) : ?Driver
    {
        $item = $this->basicFindById($id);

        return $item ? $this->newEntity($item, $withRelations) : null;
    }

    public function isUserAttached(Driver $driver) : bool
    {
        $query = "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE akun_id = :akun_id) as 'exists'";

        return $this->queryExists(
            $query,
            [ 'akun_id' => $driver->getAkun()->getId() ]
        );
    }

    public function isAssociated(Driver $driver, Akun $akun) : bool
    {
        $query = "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE akun_id = :akun_id AND id = :id) as 'exists'";

        return $this->queryExists(
            $query,
            [ 'akun_id' => $akun->getId(), 'id' => $driver->getAkun()->getId() ]
        );
    }

    public function listRuteByDriver(int|Driver $driver) : array
    {
        $query = "SELECT 
                mdo1.nama_kota as asal, 
                mdo2.nama_kota as tujuan, 
                mm.merk as merk, 
                mm.plat_nomor as plat_nomor
            FROM m_supir ms
            LEFT JOIN m_mobil mm on ms.id = mm.supir_id
            LEFT JOIN m_keberangkatan mk on mm.id = mk.mobil_id
            LEFT JOIN m_daerah_operasional mdo1 on mdo1.provinsi = mk.provinsi_id
            LEFT JOIN m_daerah_operasional mdo2 on mdo2.provinsi = mk.provinsi_id
            WHERE ms.id = :driver";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'driver' => is_int($driver) ? $driver : $driver->getId()
        ]);

        $rute = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $rute[] = [
            'asal' => $row['asal'], 'tujuan' => $row['tujuan'], 'merk' => $row['merk'], 'plat_nomor' => $row['plat_nomor']
        ];

        return $rute;
    }

    public function listTujuanByDriver(int|Driver $driver): array
    {
        $query = "SELECT nama_kota, provinsi
        FROM m_daerah_operasional
        WHERE provinsi NOT IN(SELECT mdo.provinsi
        FROM m_daerah_operasional mdo
        JOIN m_keberangkatan mk ON mk.provinsi_id = mdo.provinsi
        JOIN m_mobil mm ON mk.mobil_id = mm.id
        JOIN m_supir ms ON mm.supir_id = ms.id
        WHERE supir_id = :supir_id
        GROUP BY provinsi);";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'supir_id' => is_int($driver) ? $driver : $driver->getId()
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    protected function getTable(): string
    {
        return $this->table;
    }

    protected function newEntity(array $row, bool $withRelations = true): Driver
    {
        $akun = $withRelations
            ? (new Akun())
                ->setId($row['akun_id'])
                ->setUsername($row['akun_username'])
                ->setRole(Role::fromLabel($row['akun_role']))
            : null;

        return (new Driver())
            ->setId($row['id'])
            ->setNama($row['nama'])
            ->setKontak($row['kontak'])
            ->setPhoto(null)
            ->setAkun($akun);
    }

    public function findByAkunId(int|Akun $akun) : ?Driver
    {
        $query = "SELECT * FROM {$this->getTable()} WHERE akun_id = :akun";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['akun' => $akun->getId()]);

        return $stmt->rowCount() ? $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC), false) : null;
    }

    public function getMobil(Driver $driver) : false|array
    {
        $query = "SELECT 
            d.id, mm.merk, mm.plat_nomor, mk.provinsi_id, mk.jam_keberangkatan_id
        FROM {$this->getTable()} d
        JOIN m_mobil mm ON mm.supir_id = d.id
        JOIN m_keberangkatan mk on mm.id = mk.mobil_id
        WHERE d.id = :id
        ";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'id' => $driver->getId()
        ]);

        if ($stmt->rowCount() < 1) return false;

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'merk' => $row['merk'],
            'plat_nomor' => $row['plat_nomor'],
            'posisi' => $row['provinsi_id'],
            'jam_keberangkatan' => $row['jam_keberangkatan_id']
        ];
    }
}