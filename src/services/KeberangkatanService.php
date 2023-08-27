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
    const BATAS_PEMESANAN_SEBELUM_BERANGKAT = 30; // in minutes

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
            $lastUpdated = null;
            $jamKeberangkatan = null;
            $provinsi = null;
            foreach ($listKeberangkatan as $keberangkatan) {
                if (!is_null($keberangkatan->getMobil())) {
                    $idMobil = is_int($keberangkatan->getMobil()) ? $keberangkatan->getMobil() : $keberangkatan->getMobil()->getId();

                    if($idMobil == $mobil->getId()) {
                        $provinsi = $keberangkatan->getProvinsi()->value;
                        $jamKeberangkatan = is_int($keberangkatan->getJamKeberangkatan()) ? $keberangkatan->getJamKeberangkatan() : $keberangkatan->getJamKeberangkatan()->toArray();
                        $lastUpdated = $keberangkatan->getLastUpdated()->format('Y-m-d H:i:s');

                    }
                }
            }

            $result[] = [
                'mobil' => $mobil->toArray(),
                'provinsi' => $provinsi ?? null,
                'jam_keberangkatan' => $jamKeberangkatan ?? null,
                'last_updated' => $lastUpdated ?? null
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

    public function simpan(int $mobil, int $jamKeberangkatan, int $provinsi) : bool
    {
        $provinsi = Provinsi::fromValue($provinsi);
        if (!$provinsi) return false;

        $mobil = $this->mobilRepository->findById($mobil);
        if (!$mobil) return false;

        $jamKeberangkatan = $this->jamKeberangkatanRepository->findById($jamKeberangkatan);
        if (!$jamKeberangkatan) return false;

        $keberangkatan = new Keberangkatan();
        $keberangkatan->setProvinsi($provinsi);
        $keberangkatan->setMobil($mobil);
        $keberangkatan->setJamKeberangkatan($jamKeberangkatan);

        $this->keberangkatanRepository->updateOrCreate($keberangkatan);

        return true;
    }

    public function resetRuteMobil(int $mobil) : bool
    {
        if (false === $this->mobilRepository->exists($mobil)) return false;

        return $this->keberangkatanRepository->resetByMobil($mobil);
    }

    public function listKeberangkatanBerdasarkanJamKeberangkatan(DateTimeInterface $tanggal, int|JamKeberangkatan $jam): array
    {
        $jamKeberangkatan = $this->jamKeberangkatanRepository->findById($jam);
        if (is_null($jamKeberangkatan)) return [];

        if (false === $this->apakahJamKeberangkatanLebihDariBatasPemesanan($tanggal, $jamKeberangkatan)) return [];

        return $this->keberangkatanRepository->findByJamKeberangkatan($jamKeberangkatan);
    }

    private function apakahJamKeberangkatanLebihDariBatasPemesanan(DateTimeInterface $tanggalPemesanan, JamKeberangkatan $keberangkatan)
    {
        $tanggalKeberangkatan = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i",
            date(sprintf('Y-m-d %s', $keberangkatan->getJam(true)))
        );

        $diff = $tanggalKeberangkatan->diff($tanggalPemesanan);
        $today = ($tanggalKeberangkatan->format("Y-m-d") == $tanggalPemesanan->format("Y-m-d"));

        // handler for today
        // php is sucks
        if ($today) {
            $jamTerlewat = !$diff->invert;

            if ($jamTerlewat) return false;

            $minutes = $diff->days * 24 * 60;
            $minutes += $diff->h * 60;
            $minutes += $diff->i;

            return ($minutes > self::BATAS_PEMESANAN_SEBELUM_BERANGKAT);
        }

        // handle yesterday
        if ($diff->invert) return false;

        return true; // other days
    }
}
