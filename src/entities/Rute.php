<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
require_once __DIR__ . '/DaerahOpersional.php';

class Rute implements EntityInterface
{
    private int $id;

    private DaerahOpersional $asal;

    private DaerahOpersional $tujuan;

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
     * @return DaerahOpersional
     */
    public function getAsal(): DaerahOpersional
    {
        return $this->asal;
    }

    /**
     * @param DaerahOpersional $asal
     */
    public function setAsal(DaerahOpersional $asal): self
    {
        $this->asal = $asal;

        return $this;
    }

    /**
     * @return DaerahOpersional
     */
    public function getTujuan(): DaerahOpersional
    {
        return $this->tujuan;
    }

    /**
     * @param DaerahOpersional $tujuan
     */
    public function setTujuan(DaerahOpersional $tujuan): self
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
