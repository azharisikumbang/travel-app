<?php

require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';

class JamKeberangkatanService
{
    private JamKeberangkatanRepository $repository;

    public function __construct()
    {
        $this->repository = new JamKeberangkatanRepository();
    }

    public function tambahkanJamKeberangkatan(JamKeberangkatan $jamKeberangkatan) : bool
    {
        return $this->repository->save($jamKeberangkatan);
    }

    public function listJamKeberangkatan(): array
    {
        return $this->repository->get(10, 0);
    }
}
