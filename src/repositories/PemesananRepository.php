<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/PemesananDetailRepository.php';
require_once __DIR__ . '/../entities/Pesanan.php';
require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../enums/StatusBuktiPembayaran.php';
require_once __DIR__ . '/../enums/StatusPemesanan.php';

class PemesananRepository extends BaseRepository
{

    protected string $table = "pesanan";

    private PemesananDetailRepository $pemesananDetailRepository;

    public function __construct()
    {
        $this->pemesananDetailRepository = new PemesananDetailRepository();
    }

    protected function getTable(): string
    {
        return $this->table;
    }

    public function cekKursiTersedia(
        DateTimeInterface $tanggal,
        DaerahOperasional $asal
    ) : array
    {
        $query = "SELECT 
            p.id,
            p.tanggal_keberangkatan,
            p.tanggal_keberangkatan,
            p.jam_keberangkatan,
            p.kota_asal,
            p.kota_tujuan,
            pd.nomor_kursi,
            p.mobil_id
        FROM pesanan p
        JOIN pesanan_detail pd on p.id = pd.pesanan_id
        WHERE 
            p.tanggal_keberangkatan LIKE :tanggal_keberangkatan AND 
            p.jam_keberangkatan = :jam_keberangkatan AND
            p.kota_asal = :kota_asal AND
            p.kota_tujuan = :kota_tujuan
        ORDER BY pd.nomor_kursi";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'tanggal_keberangkatan' => $tanggal->format("Y-m-d"),
            'jam_keberangkatan' => $jam->getJam(),
            'kota_asal' => $tarif->getRute()->getNamaKota(),
            'kota_tujuan' => $tarif->getTujuan()->getNamaKota()
        ]);

        $result = [];
        if($stmt->rowCount() < 1) return $result;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function get(int $length = 10, int $from = 0, string $order = 'id', string $by = 'desc') : array
    {
        $listData = $this->getDataFromTable($this->table, $length, $from, $order, $by);

        $result = [];
        while ($row = $listData->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function getLatest() : ?Pesanan
    {
        $query = "SELECT * from {$this->getTable()} ORDER BY id DESC";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() < 1) return null;

        return $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function save(Pesanan $pesanan) : false|Pesanan
    {
        $dbh = $this->getDatabaseConnection();

        $bind = [
            'nomor_pemesanan' => $pesanan->getNomorPesanan(),
            'nomor_iterasi_pemesanan' => $pesanan->getNomorIterasiPesanan(),
            'nama_pemesan' => $pesanan->getNamaPemesan(),
            'kontak_pemesan' => $pesanan->getKontakPemesan(),
            'tanggal_keberangkatan' => $pesanan->getTanggalKeberangkatan()->format("Y-m-d"),
            'jam_keberangkatan' => $pesanan->getJamKeberangkatan(),
            'kota_asal' => $pesanan->getKotaAsal(),
            'kota_tujuan' => $pesanan->getKotaTujuan(),
            'titik_jemput' => $pesanan->getTitikJemput(),
            'tipe_penumpang' => $pesanan->getKategoriPelanggan(),
            'total_tarif' => $pesanan->getTotalTarif(),
            'status_bukti_pembayaran' => $pesanan->getStatusBuktiPembayaran()->value,
            'status_pemesanan' => $pesanan->getStatusPemesanan()->value,
            'total_uang_muka' => $pesanan->getTotalUangMuka(),
            'total_dibayarkan' => $pesanan->getTotalDibayarkan(),
            'bukti_pembayaran' => $pesanan->getBuktiPembayaran(),
            'pemesan_id' => $pesanan->getPemesanId(),
            'mobil' => $pesanan->getMobil(),
            'driver' => $pesanan->getDriver()
        ];

        $bindKeys = [];
        foreach ($bind as $attr => $value)
        {
            $bindKeys[] = ":" . $attr;
        }

        $valueKeys = implode(", ", array_keys($bind));
        $bindKeys = implode(", ", $bindKeys);

        $query = $dbh->prepare("INSERT INTO {$this->getTable()} ($valueKeys) VALUES({$bindKeys})");

        try {

            $dbh->beginTransaction();
            $query->execute($bind);

            $pesananId = $dbh->lastInsertId();
            $pesanan->setId($pesananId);

            $this->pemesananDetailRepository->saveMany($pesanan);

            $dbh->commit();
        } catch (\Exception $e) {
            $dbh->rollBack();

            die($e->getMessage());

            return false;
        }

        return $pesanan;
    }

    public function findByNomorPesanan(string $nomor, bool $detail = false) : ?Pesanan
    {
        $query = "SELECT * FROM pesanan WHERE nomor_pemesanan = :nomor_pemesanan";

        if ($detail) {
            $query = "SELECT 
                p.*,
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE nomor_pemesanan = :nomor_pemesanan";
        }

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['nomor_pemesanan' => $nomor]);

        if($stmt->rowCount() < 1) return null;
        if (!$detail) return $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC));

        $pesanan = null;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(is_null($pesanan)) $pesanan = $this->newEntity($row);

            $pesananDetail = new PesananDetail();
            $pesananDetail
                ->setId($row['detail_pemesanan_id'])
                ->setPesananId($pesanan->getId())
                ->setNomorKursi($row['nomor_kursi'])
                ->setHargaTiket($row['harga_tiket']);

            $pesanan->addPesananDetail($pesananDetail);
        }

        return $pesanan;
    }

    public function findByNomorPesananAndPelanggan(string $nomor, Akun $akun) : ?Pesanan
    {
        $query = "SELECT 
                p.*,
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE nomor_pemesanan = :nomor_pemesanan AND pemesan_id = :pemesan_id";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'nomor_pemesanan' => $nomor,
            'pemesan_id' => $akun->getId()
        ]);

        if($stmt->rowCount() < 1) return null;

        $pesanan = null;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(is_null($pesanan)) $pesanan = $this->newEntity($row);

            $pesananDetail = new PesananDetail();
            $pesananDetail
                ->setId($row['detail_pemesanan_id'])
                ->setPesananId($pesanan->getId())
                ->setNomorKursi($row['nomor_kursi'])
                ->setHargaTiket($row['harga_tiket']);

            $pesanan->addPesananDetail($pesananDetail);
        }

        return $pesanan;
    }


    public function findByTanggalKeberangkatan(DateTimeInterface $date, int $total = 10, int $from = 0, bool $withRelations = false) : array
    {
        $query = "SELECT * from {$this->getTable()} WHERE tanggal_keberangkatan LIKE :tanggal_keberangkatan ORDER BY tanggal_keberangkatan DESC LIMIT {$from}, {$total}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            "tanggal_keberangkatan" => sprintf("%s%s", $date->format('Y-m-d'), "%")
        ]);

        if($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function filterPesanan(?DateTimeInterface $date, ?DaerahOperasional $asal, ?DaerahOperasional $tujuan, int $total = 10, int $from = 0, bool $withRelations = false): array
    {
        $where = [];
        $whereString = "";

        if ($date) $where['tanggal_keberangkatan'] = sprintf("%s%s", $date->format('Y-m-d'), "%");
        if ($asal) $where['kota_asal'] = $asal->getNamaKota();
        if ($tujuan) $where['kota_tujuan'] = $tujuan->getNamaKota();

        if ($where) $whereString .= "WHERE";
        foreach ($where as $key => $value) $whereString .= sprintf(" %s = :%s AND", $key, $key);

        $query = sprintf("SELECT * from {$this->getTable()} %s ORDER BY tanggal_keberangkatan DESC LIMIT {$from}, {$total}", rtrim($whereString, "AND"));

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute($where);

        if($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function filterJadwal(?DateTimeInterface $date, ?DaerahOperasional $asal, ?DaerahOperasional $tujuan, int $total = 10, int $from = 0, bool $withRelations = false): array
    {
        $where = ['status_bukti_pembayaran' => StatusBuktiPembayaran::VALID->value];
        $whereString = "";

        if ($date) $where['tanggal_keberangkatan'] = sprintf("%s%s", $date->format('Y-m-d'), "%");
        if ($asal) $where['kota_asal'] = $asal->getNamaKota();
        if ($tujuan) $where['kota_tujuan'] = $tujuan->getNamaKota();

        if ($where) $whereString .= "WHERE";
        foreach ($where as $key => $value) $whereString .= sprintf(" %s = :%s AND", $key, $key);

        $query = sprintf("SELECT * from {$this->getTable()} %s ORDER BY tanggal_keberangkatan DESC LIMIT {$from}, {$total}", rtrim($whereString, "AND"));

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute($where);

        if($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

        return $result;
    }

    public function updateInformasiPemesanan(Pesanan $pesanan) : void
    {
        $query = "UPDATE {$this->getTable()} 
            SET nama_pemesan = :nama_pemesan,
                kontak_pemesan = :kontak_pemesan,
                titik_jemput = :titik_jemput
            WHERE nomor_pemesanan = :nomor_pemesanan";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'nama_pemesan' => $pesanan->getNamaPemesan(),
            'kontak_pemesan' => $pesanan->getKontakPemesan(),
            'titik_jemput' => $pesanan->getTitikJemput(),
            'nomor_pemesanan' => $pesanan->getNomorPesanan()
        ]);
    }

    public function updateStatusPembayaran(Pesanan $pesanan, StatusBuktiPembayaran $status): void
    {
        $query = "UPDATE {$this->getTable()} 
            SET status_bukti_pembayaran = :status_bukti_pembayaran
            WHERE nomor_pemesanan = :nomor_pemesanan";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'status_bukti_pembayaran' => $pesanan->getStatusBuktiPembayaran()->value,
            'nomor_pemesanan' => $pesanan->getNomorPesanan()
        ]);
    }

    public function updateStatusPemesananToBatal(Pesanan $pesanan): bool
    {
        $query = "UPDATE {$this->getTable()} 
            SET status_pemesanan = :status_pemesanan
            WHERE nomor_pemesanan = :nomor_pemesanan";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        return $stmt->execute([
            'status_pemesanan' => $pesanan->getStatusPemesanan()->value,
            'nomor_pemesanan' => $pesanan->getNomorPesanan()
        ]);
    }

    public function getJadwalKeberangkatanByPemesanId(Akun $user, int $length = 10, int $from = 0): array
    {
        $query = "SELECT 
                p.*,
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            LEFT JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE pemesan_id = :pemesan_id 
              AND status_bukti_pembayaran IN (:unconfirmed, :valid)
              AND total_dibayarkan >= (total_tarif / 2)
              AND tanggal_keberangkatan >= CURDATE()
            ORDER BY tanggal_keberangkatan DESC
            LIMIT {$from}, {$length}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'pemesan_id' => $user->getId(),
            'unconfirmed' => StatusBuktiPembayaran::UNCONFIRMED->value,
            'valid' => StatusBuktiPembayaran::VALID->value
        ]);

        if($stmt->rowCount() < 1) return [];

        $listPesanan = [];
        $pesanan = null;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pesanan = ($pesanan?->getId() == $row['id']) ?  $pesanan : $this->newEntity($row);

            $pesananDetail = new PesananDetail();
            $pesananDetail
                ->setId($row['detail_pemesanan_id'])
                ->setPesananId($pesanan->getId())
                ->setNomorKursi($row['nomor_kursi'])
                ->setHargaTiket($row['harga_tiket']);

            $pesanan->addPesananDetail($pesananDetail);
            $listPesanan[$pesanan->getNomorPesanan()] = $pesanan;
        }

        return array_values($listPesanan);
    }

    public function saveInformasiPembayaran(Pesanan $pesanan): void
    {
        $query = "UPDATE {$this->getTable()} 
            SET nama_pembayaran = :nama_pembayaran,
                bank_pembayaran = :bank_pembayaran,
                total_dibayarkan = :total_dibayarkan,
                bukti_pembayaran = :bukti_pembayaran,
                status_bukti_pembayaran = :status_bukti_pembayaran
            WHERE nomor_pemesanan = :nomor_pemesanan";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(params: [
            'nama_pembayaran' => $pesanan->getNamaPembayaran(),
            'bank_pembayaran' => $pesanan->getBankPembayaran(),
            'total_dibayarkan' => $pesanan->getTotalDibayarkan(),
            'bukti_pembayaran' => $pesanan->getBuktiPembayaran(),
            'status_bukti_pembayaran' => $pesanan->getStatusBuktiPembayaran()->value,
            'nomor_pemesanan' => $pesanan->getNomorPesanan()
        ]);
    }

    public function getListKursiPesananByTanggalKeberangkatanAndRute(DateTimeInterface $tanggal, Rute $rute): array
    {

    }

    protected function newEntity(array $row, bool $withRelations = false) : Pesanan
    {
        return (new Pesanan())
            ->setId($row['id'])
            ->setNomorPesanan($row['nomor_pemesanan'])
            ->setNomorIterasiPesanan($row['nomor_iterasi_pemesanan'])
            ->setNamaPemesan($row['nama_pemesan'])
            ->setKontakPemesan($row['kontak_pemesan'])
            ->setTanggalPemesanan(date_create($row['tanggal_pemesanan']))
            ->setTanggalKeberangkatan(date_create($row['tanggal_keberangkatan']))
            ->setJamKeberangkatan($row['jam_keberangkatan'])
            ->setKotaAsal($row['kota_asal'])
            ->setKotaTujuan($row['kota_tujuan'])
            ->setKategoriPelanggan($row['tipe_penumpang'])
            ->setTitikJemput($row['titik_jemput'])
            ->setTotalTarif($row['total_tarif'])
            ->setStatusBuktiPembayaran(StatusBuktiPembayaran::fromLabel($row['status_bukti_pembayaran']))
            ->setStatusPemesanan(StatusPemesanan::fromLabel($row['status_pemesanan']))
            ->setTotalUangMuka($row['total_uang_muka'])
            ->setTotalDibayarkan($row['total_dibayarkan'])
            ->setBuktiPembayaran($row['bukti_pembayaran'])
            ->setNamaPembayaran($row['nama_pembayaran'])
            ->setBankPembayaran($row['bank_pembayaran'])
            ->setPemesanId($row['pemesan_id'])
            ->setFileTiket($row['file_tiket'])
            ->setDriver($row['driver'])
            ->setMobil($row['mobil']);
    }

    public function getDailyPesananByDriver(DriverRepository $driverRepository, Driver $driver) : array
    {
        $listRute = $driverRepository->listTujuanByDriver($driver);

        $tujuan = implode(', ', array_map(fn ($item) => "'" . $item['nama_kota'] . "'", $listRute));

        $query = "SELECT p.*, 
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE DATE(tanggal_keberangkatan) = CURDATE()
            AND status_bukti_pembayaran = :status
            AND kota_tujuan IN ($tujuan)
            ORDER BY p.kota_asal";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['status' => StatusBuktiPembayaran::VALID->value]);

        return $this->extracted($stmt);
    }

    public function getRiwayatPesananByDriver(Driver $driver, int $total, int $start): array
    {
        $query = "SELECT
            p.tanggal_keberangkatan,
            p.jam_keberangkatan,
            p.mobil,
            p.kota_asal,
            p.kota_tujuan,
            COUNT(pd.id) as jumlah_penumpang
        FROM pesanan p
        LEFT JOIN pesanan_detail pd on p.id = pd.pesanan_id
        WHERE driver = :driver AND status_bukti_pembayaran = :status
        GROUP BY tanggal_keberangkatan
        LIMIT {$total} OFFSET {$start}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['status' => StatusBuktiPembayaran::PENDING->value, 'driver' => $driver->getNama()]);

        if ($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                'tanggal_keberangkatan' => $row['tanggal_keberangkatan'],
                'mobil' => $row['mobil'],
                'jam_keberangkatan' => $row['jam_keberangkatan'],
                'kota_asal' => $row['kota_asal'],
                'kota_tujuan' => $row['kota_tujuan'],
                'jumlah_penumpang' => $row['jumlah_penumpang']
            ];
        }

        return $result;
    }


    public function getBuktiPembayaran(string $nomorPemesanan, ?Akun $akun) : false|string
    {
        $where = "";
        $bind = [
            'status_bukti_pembayaran' => StatusBuktiPembayaran::PENDING->value,
            'nomor_pemesanan' => $nomorPemesanan
        ];

        if ($akun) {
            $bind['pemesan_id'] = $akun->getId();
            $where .= " AND pemesan_id = :pemesan_id ";
        }

        $query = "SELECT id, bukti_pembayaran 
            FROM {$this->getTable()} 
            WHERE NOT status_bukti_pembayaran = :status_bukti_pembayaran 
            AND nomor_pemesanan = :nomor_pemesanan {$where}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute($bind);

        return $stmt->rowCount() ? $stmt->fetch(PDO::FETCH_ASSOC)['bukti_pembayaran'] : false;
    }

    public function updateInformasiFileTiket(Pesanan $pesanan) : void
    {
        $query = "UPDATE {$this->getTable()} SET file_tiket = :file_tiket WHERE nomor_pemesanan = :nomor_pemesanan";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'file_tiket' => $pesanan->getFileTiket(),
            'nomor_pemesanan' => $pesanan->getNomorPesanan()
        ]);
    }

    public function getFileTiket(string $nomorPemesanan, ?Akun $akun = null) : false|string
    {
        $where = "";
        $bind = [
            'status_bukti_pembayaran' => StatusBuktiPembayaran::VALID->value,
            'nomor_pemesanan' => $nomorPemesanan
        ];

        if ($akun) {
            $bind['pemesan_id'] = $akun->getId();
            $where .= " AND pemesan_id = :pemesan_id ";
        }

        $query = "SELECT id, file_tiket 
            FROM {$this->getTable()} 
            WHERE status_bukti_pembayaran = :status_bukti_pembayaran 
            AND nomor_pemesanan = :nomor_pemesanan {$where}
            LIMIT 1";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute($bind);

        return $stmt->rowCount() ? $stmt->fetch(PDO::FETCH_ASSOC)['file_tiket'] : false;
    }

    public function getListPesananHariIniByAsalKota(string $asal) : array
    {
        $query = "SELECt p.tanggal_keberangkatan, p.mobil,
                p.id, 
                p.kota_asal, 
                p.nomor_pemesanan,
                pd.nomor_kursi as pd_nomor_kursi
        FROM pesanan p
        JOIN pesanan_detail pd on p.id = pd.pesanan_id
        WHERE p.tanggal_keberangkatan = CURDATE() AND NOT p.status_pemesanan = :batal";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['batal' => StatusPemesanan::BATAL->value]);

        if ($stmt->rowCount() < 1) return [];

        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($result[$row['id']])) $result[$row['id']] = [
                'id' => $row['id'],
                'mobil' => $row['mobil'],
                'tanggal_keberangkatan' => $row['tanggal_keberangkatan'],
                'asal' => $row['kota_asal'],
                'list_kursi_dipesan' => []
            ];

            $result[$row['id']]['list_kursi_dipesan'][] = $row['pd_nomor_kursi'];
        }

        return $result;
    }

    public function getByPemesan(Pelanggan|Akun $pelanggan) : array
    {
        $id = ($pelanggan instanceof Pelanggan) ? $pelanggan->getAkun()->getId() : $pelanggan->getId();

        $query = "SELECT 
                p.*,
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            LEFT JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE pemesan_id = :pemesan_id
            ORDER BY tanggal_keberangkatan DESC";


        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['pemesan_id' => $id]);

        return $this->extracted($stmt);
    }

    /**
     * @param false|PDOStatement $stmt
     * @return array
     */
    public function extracted(false|PDOStatement $stmt): array
    {
        if ($stmt->rowCount() < 1) return [];

        $listPesanan = [];
        $pesanan = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pesanan = ($pesanan?->getId() == $row['id']) ? $pesanan : $this->newEntity($row);

            $pesananDetail = new PesananDetail();
            $pesananDetail
                ->setId($row['detail_pemesanan_id'])
                ->setPesananId($pesanan->getId())
                ->setNomorKursi($row['nomor_kursi'])
                ->setHargaTiket($row['harga_tiket']);

            $pesanan->addPesananDetail($pesananDetail);
            $listPesanan[$pesanan->getNomorPesanan()] = $pesanan;
        }

        return $listPesanan;
    }

    public function getPesananMenungguPembayaran($user, $total, $from)
    {
        $query = "SELECT 
                p.*,
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            LEFT JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE pemesan_id = :pemesan_id 
              AND status_bukti_pembayaran IN (:unconfirmed, :pending) 
              AND total_dibayarkan < total_tarif
              AND tanggal_keberangkatan >= CURDATE()
            ORDER BY tanggal_keberangkatan DESC
            LIMIT {$from}, {$total}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'pemesan_id' => $user->getId(),
            'unconfirmed' => StatusBuktiPembayaran::UNCONFIRMED->value,
            'pending' => StatusBuktiPembayaran::PENDING->value
        ]);

        if($stmt->rowCount() < 1) return [];

        $listPesanan = [];
        $pesanan = null;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pesanan = ($pesanan?->getId() == $row['id']) ?  $pesanan : $this->newEntity($row);

            $pesananDetail = new PesananDetail();
            $pesananDetail
                ->setId($row['detail_pemesanan_id'])
                ->setPesananId($pesanan->getId())
                ->setNomorKursi($row['nomor_kursi'])
                ->setHargaTiket($row['harga_tiket']);

            $pesanan->addPesananDetail($pesananDetail);
            $listPesanan[$pesanan->getNomorPesanan()] = $pesanan;
        }

        return array_values($listPesanan);
    }

    public function getPesananPelangganByFilter(Akun $auth, array $filter) : array
    {
        $from = $filter['pagination']['offset'] ?? 0;
        $total = $filter['pagination']['total'] ?? 10;
        $search = $filter['nomor_pemesanan'];
        $tanggal = $filter['tanggal_keberangkatan'] ? $filter['tanggal_keberangkatan']->format('Y-m-d') . '%' : null;

        $where = [];
        if ($tanggal) $where['tanggal_keberangkatan'] = $tanggal;
        if ($search) $where['nomor_pemesanan'] = $search;

        $whereQuery = "";
        foreach ($where as $colomn => $value) {
            $whereQuery .= " AND $colomn LIKE :$colomn";
        }

        $whereQuery = rtrim($whereQuery, "AND");

        $query = "SELECT 
                p.*,
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            LEFT JOIN pesanan_detail pd on p.id = pd.pesanan_id
            WHERE pemesan_id = :pemesan_id {$whereQuery} 
            ORDER BY tanggal_keberangkatan DESC
            LIMIT {$from}, {$total}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute([
            'pemesan_id' => $auth->getId(),
            ...$where
        ]);

        if($stmt->rowCount() < 1) return [];

        $listPesanan = [];
        $pesanan = null;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pesanan = ($pesanan?->getId() == $row['id']) ?  $pesanan : $this->newEntity($row);

            $pesananDetail = new PesananDetail();
            $pesananDetail
                ->setId($row['detail_pemesanan_id'])
                ->setPesananId($pesanan->getId())
                ->setNomorKursi($row['nomor_kursi'])
                ->setHargaTiket($row['harga_tiket']);

            $pesanan->addPesananDetail($pesananDetail);
            $listPesanan[$pesanan->getNomorPesanan()] = $pesanan;
        }

        return array_values($listPesanan);
    }

    public function getDataForLaporanPenjualan(string $kriteria)
    {
        $queryTanggalPemesanan = "";
        $appendQuery = "";
        $hasWhere = false;
        switch ($kriteria) {
            case 'today':
                $queryTanggalPemesanan = "DATE(p.tanggal_keberangkatan) = CURDATE()";
                $hasWhere = true;
                break;
            case 'month':
                $hasWhere = true;
                $queryTanggalPemesanan = "MONTH(p.tanggal_keberangkatan) = MONTH(CURRENT_DATE()) AND YEAR(p.tanggal_keberangkatan) = YEAR(CURRENT_DATE())";
                break;
            default:
                $hasWhere = false;
                break;
        }

        if ($hasWhere) {
            $appendQuery = "WHERE $queryTanggalPemesanan";
        }

        $query = "SELECT p.*, 
                pd.id as detail_pemesanan_id,
                pd.nomor_kursi,
                pd.harga_tiket
            FROM pesanan p
            JOIN pesanan_detail pd on p.id = pd.pesanan_id {$appendQuery}";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        return  $this->extracted($stmt);
    }

    public function getDataForLaporanPejualanByPeriode(int $year, int $month, int $date = 0): array
    {
        $where = $date > 0 ?
            sprintf("DATE(tanggal_keberangkatan) = '%s-%s-%s'", $year, $month, $date)
            : sprintf("YEAR(tanggal_keberangkatan) = '%s' AND MONTH(tanggal_keberangkatan) = '%s'", $year, $month);

        $query = "SELECT
                p.tipe_penumpang,
                (SELECT SUM(m.total_dibayarkan) FROM pesanan m WHERE status_pemesanan != 'BATAL' AND status_bukti_pembayaran = 'VALID'
                 AND m.tipe_penumpang = p.tipe_penumpang AND {$where}
                    ) as uang_masuk,
                (SELECT SUM(k.total_tarif / 2) FROM pesanan k WHERE status_pemesanan = 'BATAL' AND status_bukti_pembayaran = 'VALID'
                AND k.tipe_penumpang = p.tipe_penumpang AND {$where}
                ) as uang_keluar,
                (SELECT COUNT(*) FROM pesanan b WHERE status_pemesanan = 'BATAL' AND b.tipe_penumpang = p.tipe_penumpang AND {$where}) as jumlah_batal
            FROM pesanan p
            group by tipe_penumpang";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() < 1) return [];

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }
}