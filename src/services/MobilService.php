<?php

require_once __DIR__ . '/../repositories/MobilRepository.php';
require_once __DIR__ . '/../entities/Mobil.php';

class MobilService
{
    private MobilRepository $mobilRepository;

    public function __construct()
    {
        $this->mobilRepository = new MobilRepository();
    }

    public function tambahkanMobilOperasional(Mobil $mobil)
    {
        $this->mobilRepository->save($mobil);
    }

    public function listMobilOperasional(): array
    {
        return $this->mobilRepository->get(10, 0);
    }
}