<?php

require_once __DIR__ . '/../repositories/DaerahOperasionalRepository.php';
require_once __DIR__ . '/../entities/DaerahOperasional.php';

class DaerahOperasionalService
{
    private DaerahOperasionalRepository $daerahOperasionalRepository;

    public function __construct()
    {
        $this->daerahOperasionalRepository = new DaerahOperasionalRepository();
    }

    public function listDaerahOperasional(int $length = 10, int $from = 0) : array
    {
        return $this->daerahOperasionalRepository->get($length, $from);
    }

    public function tambahkanDaerahOperasional(DaerahOperasional $daerah) : void
    {
        $this->daerahOperasionalRepository->save($daerah);
    }

    public function simpan(DaerahOperasional $daerahOpersional) : bool
    {
        return $this->daerahOperasionalRepository->updateOrCreate($daerahOpersional);
    }

    public function hapus(int|DaerahOperasional $daerahOpersional) : void
    {
        $this->daerahOperasionalRepository->delete($daerahOpersional);
    }
}