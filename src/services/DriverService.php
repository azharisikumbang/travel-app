<?php

require_once __DIR__ . '/../repositories/AkunRepository.php';
require_once __DIR__ . '/../repositories/DriverRepository.php';
require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../entities/Driver.php';
require_once __DIR__ . '/../enums/Provinsi.php';

class DriverService
{
    private DriverRepository $driverRepository;
    private AkunRepository $akunRepository;
    private JamKeberangkatanRepository $jamKeberangkatanRepository;


    public function __construct()
    {
        $this->akunRepository = new AkunRepository();
        $this->driverRepository = new DriverRepository();
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
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

    public function listRuteSaya(Driver|Akun $driver): array
    {
        if ($driver instanceof Akun) $driver = $this->driverRepository->findByAkunId($driver);

        return $this->driverRepository->listTujuanByDriver($driver);
    }

    public function getMobilDanRuteSaya(Driver $driver): array
    {
        $mobil = $this->driverRepository->getMobil($driver);
        if (!$mobil) return [];

        $mobil['jam_keberangkatan'] = $this->jamKeberangkatanRepository->findById($mobil['jam_keberangkatan'])->toArray();
        $mobil['posisi'] = Provinsi::fromValue($mobil['posisi'])->getDisplayName();

        return $mobil;
    }

    public function findByAkun(Akun $akun)
    {
        return $this->driverRepository->findByAkunId($akun);
    }
}