<?php

require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/PemesananDetailRepository.php';
require_once __DIR__ . '/../entities/Pesanan.php';
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
        JamKeberangkatan $jam,
        Tarif $tarif
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
            'kota_asal' => $tarif->getAsal()->getNamaKota(),
            'kota_tujuan' => $tarif->getTujuan()->getNamaKota()
        ]);

        $result = [];
        if($stmt->rowCount() < 1) return $result;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row);

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

    public function save(Pesanan $pesanan) : Pesanan
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
            'tipe_penumpang' => $pesanan->getTipePenumpang(),
            'total_tarif' => $pesanan->getTotalTarif(),
            'status_bukti_pembayaran' => $pesanan->getStatusBuktiPembayaran()->value,
            'status_pemesanan' => $pesanan->getStatusPemesanan()->value,
            'total_uang_muka' => $pesanan->getTotalUangMuka(),
            'total_dibayarkan' => $pesanan->getTotalDibayarkan(),
            'bukti_pembayaran' => $pesanan->getBuktiPembayaran(),
            'pemesan_id' => $pesanan->getPemesanId(),
            'mobil_id' => $pesanan->getMobilId()
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

    private function newEntity(array $row) : Pesanan
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
            ->setTipePenumpang($row['tipe_penumpang'])
            ->setTitikJemput($row['titik_jemput'])
            ->setTotalTarif($row['total_tarif'])
            ->setStatusBuktiPembayaran(StatusBuktiPembayaran::getLabel($row['status_bukti_pembayaran']))
            ->setStatusPemesanan(StatusPemesanan::getLabel($row['status_pemesanan']))
            ->setTotalUangMuka($row['total_uang_muka'])
            ->setTotalDibayarkan($row['total_dibayarkan'])
            ->setBuktiPembayaran($row['bukti_pembayaran'])
            ->setNamaPembayaran($row['nama_pembayaran'])
            ->setBankPembayaran($row['bank_pembayaran'])
            ->setPemesanId($row['pemesan_id'])
            ->setMobilId($row['mobil_id']);
    }
}