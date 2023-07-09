<?php

require_once __DIR__ . '/../repositories/KeberangkatanRepository.php';
require_once __DIR__ . '/../repositories/DriverRepository.php';
require_once __DIR__ . '/../repositories/RuteRepository.php';
require_once __DIR__ . '/../repositories/MobilRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Keberangkatan.php';
require_once __DIR__ . '/../entities/Rute.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';

class KeberangkatanService
{
    private KeberangkatanRepository $keberangkatanRepository;

    private MobilRepository $mobilRepository;

    private RuteRepository $ruteRepository;

    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    public function __construct()
    {
        $this->keberangkatanRepository = new KeberangkatanRepository();
        $this->mobilRepository = new MobilRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
        $this->ruteRepository = new RuteRepository();
    }

    public function listKeberangkatanHarian() : array
    {
        $listMobil = $this->mobilRepository->get(new DriverRepository(), 100, 0);
        $listKeberangkatan = $this->keberangkatanRepository->get(true);

        $result = [];
        /** @var $mobil Mobil */
        /** @var $keberangkatan Keberangkatan */
        foreach ($listMobil as $mobil) {
            $listRute = [];
            $jamKeberangkatan = null;
            foreach ($listKeberangkatan as $keberangkatan) {
                if (!is_null($keberangkatan->getMobil())) {
                    $idMobil = is_int($keberangkatan->getMobil()) ? $keberangkatan->getMobil() : $keberangkatan->getMobil()->getId();

                    if($idMobil == $mobil->getId()) {
                        $rute = is_int($keberangkatan->getRute()) ? $keberangkatan->getRute() : $keberangkatan->getRute()->toArray();
                        $jamKeberangkatan = is_int($keberangkatan->getJamKeberangkatan()) ? $keberangkatan->getJamKeberangkatan() : $keberangkatan->getJamKeberangkatan()->toArray();
                        $lastUpdated = $keberangkatan->getLastUpdated()->format('Y-m-d H:i:s');

                        $listRute[] = $rute;
                    }
                }
            }

            $result[] = [
                'mobil' => $mobil->toArray(),
                'rute' => $listRute,
                'jam_keberangkatan' => $jamKeberangkatan,
                'last_updated' => $lastUpdated
            ];
        }

        return $result;
    }

    public function listKeberangkatanGrupBerdasarkanMobil(bool $withRelations = false) : array
    {
        $list = $this->keberangkatanRepository->get($withRelations);

        $result = [];
        foreach ($list as $item) {
            if (is_null($item->getMobil())) continue;

            $result = [
                'mobil_id' => is_int($item->getMobil()) ? $item->getMobil() : $item->getMobil()->getId(),
                'list_keberangkatan' => $item
            ];
        }
    }

    public function simpan(int $mobil, int $jamKeberangkatan, array $listRute) : bool
    {
        $mobil = $this->mobilRepository->findById($mobil);
        if (!$mobil) return false;

        $jamKeberangkatan = $this->jamKeberangkatanRepository->findById($jamKeberangkatan);
        if (!$jamKeberangkatan) return false;

        $existsRute = [];
        foreach ($listRute as $rute) {
            $rute = $this->ruteRepository->findById($rute);
            if (is_null($rute)) return false;

            $existsRute[] = $rute;
        }

        $this->keberangkatanRepository->update($mobil, $jamKeberangkatan, $existsRute);

        return true;
    }

    public function resetRuteMobil(int $mobil) : bool
    {
        if (false === $this->mobilRepository->exists($mobil)) return false;

        return $this->keberangkatanRepository->resetByMobil($mobil);
    }
}
