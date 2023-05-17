<?php

require_once __DIR__ . '/../repositories/TipePenumpangRepository.php';
require_once __DIR__ . '/../entities/TipePenumpang.php';

class TipePenumpangService
{
    private TipePenumpangRepository $tipePenumpangRepository;

    public function __construct()
    {
        $this->tipePenumpangRepository = new TipePenumpangRepository();
    }

    public function tambahkanTipePenumpang(TipePenumpang $tipePenumpang)
    {
        $this->tipePenumpangRepository->save($tipePenumpang);
    }

    public function listTipePenumpang(): array
    {
        return $this->tipePenumpangRepository->get(10, 0);
    }
}