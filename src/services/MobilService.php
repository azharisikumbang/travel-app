<?php

require_once __DIR__ . '/../repositories/MobilRepository.php';
require_once __DIR__ . '/../repositories/DriverRepository.php';
require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Driver.php';

class MobilService
{
    private MobilRepository $mobilRepository;

    private DriverRepository $driverRepository;

    public function __construct()
    {
        $this->mobilRepository = new MobilRepository();
        $this->driverRepository = new DriverRepository();
     }

    public function listMobil(int $length = 10, int $from = 0) : array
    {
        return $this->mobilRepository->get($this->driverRepository, $length, $from);
    }

    public function simpan(int $id, string $merk, string $platNomor, int $jumlahKursi, int $driver) : false|Mobil
    {
        if ($jumlahKursi < 1) return false;

        $driver = $this->driverRepository->findById($driver);
        if (is_null($driver)) return false;

        $mobil = new Mobil();
        $mobil->setId($id);
        $mobil->setMerk($merk);
        $mobil->setPlatNomor($platNomor);
        $mobil->setJumlahKursi($jumlahKursi);
        $mobil->setDriver($driver);

        return $this->mobilRepository->updateOrCreate($mobil);
    }

    public function hapus(int|Mobil $mobil) : void
    {
        $this->mobilRepository->deleteById($mobil);
    }
}