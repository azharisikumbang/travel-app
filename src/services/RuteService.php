<?php

require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../repositories/RuteRepository.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';
require_once __DIR__ . '/../entities/Rute.php';

class RuteService
{
    private RuteRepository $ruteRepository;
    private DaerahOperasionalRepository $daerahOperasionalRepository;

    public function __construct()
    {
        $this->daerahOperasionalRepository = new DaerahOperasionalRepository();
        $this->ruteRepository = new RuteRepository();
    }

    public function listRute(int $length = 10, int $from = 0) : array
    {
        return $this->ruteRepository->get($length, $from);
    }

    public function cekRuteTersedia(int $asal, int $tujuan) : false|array
    {
        if (($asal == $tujuan) || $asal < 1 || $tujuan < 1) return  false;

        $reversed = false;
        $rute = $this->ruteRepository->getByAsalAndTujuanOrReversed(
            (new DaerahOperasional())->setId($asal),
            (new DaerahOperasional())->setId($tujuan)
        );

        if (!$rute) return false;

        if ($this->isAsalAndTujuanReversed($rute, $asal, $tujuan)) $reversed = true;

        return [
            'rute' => $rute,
            'reversed' => $reversed
        ];
    }

    public function simpan(int $rute, int $asal, int $tujuan) : false|Rute
    {
        if ($asal == $tujuan) return false;

        $asalEntity = $this->daerahOperasionalRepository->findById($asal);
        if (is_null($asalEntity)) return false;

        $tujuanEntity = $this->daerahOperasionalRepository->findById($tujuan);
        if (is_null($tujuanEntity)) return false;

        // save
        $ruteEntity = $this->ruteRepository->findById($rute);
        if (is_null($ruteEntity)) {
            $ruteEntity = new Rute();
            $ruteEntity->setAsal($asalEntity);
            $ruteEntity->setTujuan($tujuanEntity);

            return $this->ruteRepository->save($ruteEntity);
        }

        $ruteEntity->setAsal($asalEntity);
        $ruteEntity->setTujuan($tujuanEntity);

        return $this->ruteRepository->update($ruteEntity);

    }

    public function hapus(int|Rute $rute) : void
    {
        $this->ruteRepository->deleteById($rute);
    }

    private function isAsalAndTujuanReversed(
        Rute $rute,
        int $asal,
        int $tujuan
    ) : bool {
        return ($rute->getAsal()->getId() == $tujuan) && $rute->getTujuan()->getId() == $asal;
    }
}