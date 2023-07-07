<?php

require_once __DIR__ . '/../entities/TipePenumpang.php';
require_once __DIR__ . '/../entities/DaerahOpersional.php';
require_once __DIR__ . '/../entities/Tarif.php';
require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../repositories/TipePenumpangRepository.php';
require_once __DIR__ . '/../repositories/TarifRepository.php';
require_once __DIR__ . '/../repositories/KeberangkatanRepository.php';

class TarifService
{
    private DaerahOperasionalRepository $daerahOperasionalRepository;

    private TipePenumpangRepository $tipePenumpangRepository;

    private TarifRepository $tarifRepository;

    private KeberangkatanRepository $ruteRepository;

    public function __construct()
    {
        $this->daerahOperasionalRepository = new DaerahOperasionalRepository();
        $this->tipePenumpangRepository = new TipePenumpangRepository();
        $this->tarifRepository = new TarifRepository($this->tipePenumpangRepository, $this->daerahOperasionalRepository);
        $this->ruteRepository = new KeberangkatanRepository();
    }

    public function buatTarif(
        float $nominal,
        int|TipePenumpang $tipe,
        int|DaerahOpersional $asal,
        int|DaerahOpersional $tujuan
    ) : bool|Tarif {
        if ($asal == $tujuan) return false;

        if(is_int($tipe)) $tipe = $this->tipePenumpangRepository->findById($tipe);
        if(is_int($asal)) $asal = $this->daerahOperasionalRepository->findById($asal);
        if(is_int($tujuan)) $tujuan = $this->daerahOperasionalRepository->findById($tujuan);

        if ($asal->getId() == $tujuan->getId()) return false;

        return (new Tarif())
            ->setTarif($nominal)
            ->setTipePenumpang($tipe)
            ->setAsal($asal)
            ->setTujuan($tujuan);
    }

    public function simpanTarifBaru(Tarif $tarif) : bool
    {
        if(false === $this->ruteRepository->isAsalAndTujuanExists($tarif->getAsal(), $tarif->getTujuan())) {
            $this->ruteRepository->save(
                (new RuteHarian())
                    ->setAsal($tarif->getAsal())
                    ->setTujuan($tarif->getTujuan())
                    ->setMobil(null)
                    ->setJamKeberangkatan(null)
            );
        }

        return $this->tarifRepository->save($tarif);
    }

    public function listTarifByKategori(int $length = 10, int $from = 0)
    {
        $listTarif = $this->tarifRepository->get($length, $from);

        $result = [];
        foreach ($listTarif as $tarif)
        {
            $idTipePenumpang = $tarif->getTipePenumpang()->getId();

            if(!isset($result[$idTipePenumpang])) $result[$idTipePenumpang] = [
                'tipe_penumpang_id' => $idTipePenumpang,
                'tipe_penumpang' => $tarif->getTipePenumpang()->getTipePenumpang()
            ];


            $result[$idTipePenumpang]['list_tarif'][] = $tarif->toArray();
        }

        return $result;
    }

    public function lihatDetailTarif(int $tarif) : ?Tarif
    {
        return $this->tarifRepository->findById($tarif);
    }

    public function cariTarif(int $asal, int $tujuan, int $kategori) 
    {
        return $this->tarifRepository->cariTarif($asal, $tujuan, $kategori);
    }

    public function listRuteTersedia(): false|array
    {
        return $this->tarifRepository->listRuteBerdasarkanKota();
    }
}
