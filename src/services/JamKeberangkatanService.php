<?php

require_once __DIR__ . '/../repositories/JamKeberangkatanRepository.php';
require_once __DIR__ . '/../entities/JamKeberangkatan.php';

class JamKeberangkatanService
{
    private JamKeberangkatanRepository $jamKeberangkatanRepository;

    public function __construct()
    {
        $this->jamKeberangkatanRepository = new JamKeberangkatanRepository();
    }

    public function simpan(int $id, string $jam, string $alias) : false|JamKeberangkatan
    {
        $jamKeberangkatan = new JamKeberangkatan();
        $jamKeberangkatan->setId($id);
        $jamKeberangkatan->setJam($jam);
        $jamKeberangkatan->setAlias($alias);

        return $this->jamKeberangkatanRepository->updateOrCreate($jamKeberangkatan);
    }

    public function listJamKeberangkatan(int $length = 10, int $from = 0): array
    {
        return $this->jamKeberangkatanRepository->get($length, $from);
    }

    public function hapus(int|JamKeberangkatan $jamKeberangkatan) : bool
    {
        return $this->jamKeberangkatanRepository->deleteById($jamKeberangkatan);
    }
}