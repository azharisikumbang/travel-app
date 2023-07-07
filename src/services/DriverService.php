<?php

require_once __DIR__ . '/../repositories/AkunRepository.php';
require_once __DIR__ . '/../repositories/DriverRepository.php';
require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../entities/Driver.php';

class DriverService
{
    private DriverRepository $driverRepository;
    private AkunRepository $akunRepository;

    public function __construct()
    {
        $this->akunRepository = new AkunRepository();
        $this->driverRepository = new DriverRepository();
    }

    public function listDriver(int $length = 10, int $from = 0) : array
    {
        return $this->driverRepository->get($this->akunRepository, $length, $from);
    }

    public function simpan(int|Driver $id, string $nama, string $kontak, int $akun = -1) : false|Driver
    {
        $driver = is_int($id) ? (new Driver())->setId($id) : $id;
        $driver->setNama($nama);
        $driver->setKontak($kontak);
        $driver->setAkun((new Akun())->setId($akun));

        if ($akun > 0) {
            if ($this->driverRepository->isUserAttached($driver)) return $this->driverRepository->update($driver);
        }

        return $this->driverRepository->save($driver);
    }

    public function tambahkanDriver(string $nama, string $kontak, ?string $photo, Akun $akun) : false|Driver
    {
        $driver = new Driver();
        $driver->setNama($nama);
        $driver->setKontak($kontak);
        $driver->setPhoto($photo);
        $driver->setAkun($akun);

        return $this->driverRepository->save($driver);
    }

    public function updateInformasiDriver(int|Driver $driver, string $nama, string $kontak, Akun $akun): false|Driver
    {
        $driver = is_int($driver) ? $this->driverRepository->findById($driver, false) : $driver;
        $driver->setNama($nama);
        $driver->setKontak($kontak);
        $driver->setAkun($akun);

        if (false === $this->driverRepository->isAssociated($driver, $akun)) return false;

        return $this->driverRepository->update($driver);
    }

    public function hapus(int|Rute $rute) : void
    {
        $this->driverRepository->deleteById($rute);
    }

    public function driverTerdaftar(int|Driver $driver) : false|Driver
    {
        $id = is_int($driver) ? $driver : $driver->getId();

        return $this->driverRepository->findById($id) ?? false;
    }
}