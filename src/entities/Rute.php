<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
require_once __DIR__ . '/DaerahOperasional.php';

class Rute implements EntityInterface
{
    private int $id;

    private DaerahOperasional $asal;

    private DaerahOperasional $tujuan;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DaerahOperasional
     */
    public function getAsal(): DaerahOperasional
    {
        return $this->asal;
    }

    /**
     * @param DaerahOperasional $asal
     */
    public function setAsal(DaerahOperasional $asal): self
    {
        $this->asal = $asal;

        return $this;
    }

    /**
     * @return DaerahOperasional
     */
    public function getTujuan(): DaerahOperasional
    {
        return $this->tujuan;
    }

    /**
     * @param DaerahOperasional $tujuan
     */
    public function setTujuan(DaerahOperasional $tujuan): self
    {
        $this->tujuan = $tujuan;

        return $this;
    }

    public function toArray(): array
    {
        return [
          'id' => $this->getId(),
          'asal' => $this->getAsal()->toArray(),
          'tujuan' => $this->getTujuan()->toArray()
        ];
    }

}
