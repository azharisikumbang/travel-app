<?php

require_once __DIR__ . '/../repositories/MobilRepository.php';
require_once __DIR__ . '/../repositories/AkunRepository.php';
require_once __DIR__ . '/../entities/Mobil.php';
require_once __DIR__ . '/../entities/Akun.php';

class MobilService
{
    private MobilRepository $mobilRepository;

    private AkunRepository $userRepository;

    public function __construct()
    {
        $this->mobilRepository = new MobilRepository();
        $this->userRepository = new AkunRepository();
     }

    public function tambahkanMobilOperasional(Mobil $mobil, null|int|Akun $driver = null) : false|Mobil
    {
        if (is_int($driver)) $driver = $this->userRepository->findById($driver);

        $mobil->setDriver($driver);

        return $this->mobilRepository->save($mobil) ? $mobil : false;
    }

    public function listMobilOperasional(): array
    {
        return $this->mobilRepository->get(10, 0);
    }
}