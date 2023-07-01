<?php

require_once __DIR__ . '/../repositories/PemesananRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../repositories/TipePenumpangRepository.php';
require_once __DIR__ . '/../repositories/TarifRepository.php';
require_once __DIR__ . '/../entities/Pesanan.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';
require_once __DIR__ . '/../entities/Tarif.php';

class TiketService
{
    private PemesananRepository $pemesananRepository;

    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    private TipePenumpangRepository $tipePenumpangRepository;

    private DaerahOperasionalRepository $daerahOperasionalRepository;

    private TarifRepository $tarifRepository;

    public function __construct()
    {
        $this->pemesananRepository = new PemesananRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
        $this->tipePenumpangRepository = new TipePenumpangRepository();
        $this->daerahOperasionalRepository = new DaerahOperasionalRepository();
        $this->tarifRepository = new TarifRepository($this->tipePenumpangRepository, $this->daerahOperasionalRepository);
    }

    public function cariKursiTersedia(
        DateTimeInterface $tanggal,
        JamKeberangkatan|int $jam,
        Tarif $tarif
    ): array|false
    {
        $jamKeberangkatan = is_int($jam) ? $this->jamKeberangkatanRepository->findById($jam) : $jam;

        return [
            'kriteria' => [
                'tanggal_keberangkatan' => $tanggal->format("Y-m-d"),
                'asal_keberangkatan' => $tarif->getAsal()->toArray(),
                'tujuan_keberangkatan' => $tarif->getTujuan()->toArray(),
                'jam_keberangkatan' => $jamKeberangkatan->toArray()
            ],
            'list_kursi' => [
                'tersedia' => 8,
                'dipesan' => 0,
                'total' => 8,
                'detail' => [
                    [ 'nomor' => 1, 'tersedia' => true ],
                    [ 'nomor' => 2, 'tersedia' => true ],
                    [ 'nomor' => 3, 'tersedia' => true ],
                    [ 'nomor' => 4, 'tersedia' => true ],
                    [ 'nomor' => 5, 'tersedia' => true ],
                    [ 'nomor' => 6, 'tersedia' => true ],
                    [ 'nomor' => 7, 'tersedia' => true ],
                    [ 'nomor' => 8, 'tersedia' => true ]
                ]
            ]
        ];

        return $this->pemesananRepository->cekKursiTersedia(
            $tanggal,
            $jamKeberangkatan,
            $tarif
        );
    }

    public function cekApakahAsalDanTujuanSama(DaerahOpersional|int $asal, DaerahOpersional|int $tujuan) : bool
    {
        return ($asal == $tujuan);
    }

    public function cekTanggalKeberangkatanValid(DateTimeInterface $tanggal) : bool
    {
        return (date("Y-m-d") <= $tanggal->format("Y-m-d"));
    }

    public function cekApakahRuteDanKategoriTiketTersedia(int $asal, int $tujuan, int $kategori) : bool
    {
        return $this->tarifRepository->cariTarif($asal, $tujuan, $kategori);
    }

    public function validasiKursiTersedia(
        array $kursiDipesan,
        DateTimeInterface $tanggal,
        int $jam,
        Tarif $tarif
    ) : bool {
        $listKursiTersedia = $this->cariKursiTersedia($tanggal, $jam, $tarif);

        if ($listKursiTersedia['list_kursi']['tersedia'] < 1) return false;

        $listKursi = [
            'dipesan' => $kursiDipesan,
            'tersedia' => [],
            'kriteria' => $listKursiTersedia['kriteria'],
            'list_kursi' => [],
            'total_kursi' => $listKursiTersedia['list_kursi']['total']
        ];

        foreach ($listKursiTersedia['list_kursi']['detail'] as $kursi) {
            foreach ($kursiDipesan as $dipesan) {
                if($dipesan != $kursi['nomor']) continue;

                if($kursi['tersedia'] === false) return false;
            }
        }

        return true; // @TODO: return lebih detail nomor kursi dan statusnya
    }
}