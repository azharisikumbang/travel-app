<?php

require_once __DIR__ . '/../repositories/KeberangkatanRepository.php';
require_once __DIR__ . '/../repositories/MobilRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/RuteHarian.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';

class KeberangkatanService
{
    private KeberangkatanRepository $keberangkatanRepository;

    private MobilRepository $mobilRepository;

    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    public function __construct()
    {
        $this->keberangkatanRepository = new KeberangkatanRepository();
        $this->mobilRepository = new MobilRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
    }

    public function listRuteTersedia(int $total = 50, int $from = 0): array
    {
        return $this->keberangkatanRepository->get();
    }

    public function updateRuteHarian(int|RuteHarian $rute, int|Mobil $mobil, int|JamKeberangkatan $jam): bool
    {
        if (false == $this->keberangkatanRepository->exists($rute)) return false;
        if (false == $this->mobilRepository->exists($mobil)) return false;
        if (false == $this->jamKeberangkatanRepository->exists($jam)) return false;

        $rute = (is_int($rute))
            ? (new RuteHarian())->setId($rute)
            : $rute;

        $mobil = (is_int($mobil))
            ? (new Mobil())->setId($mobil)
            : $mobil;

        $jam = (is_int($jam))
            ? (new JamKeberangkatan())->setId($jam)
            : $jam;

        return $this->keberangkatanRepository->updateMobilAndJam($rute, $mobil, $jam);
    }
}
