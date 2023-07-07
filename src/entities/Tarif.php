<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class Tarif implements EntityInterface
{
    private int $id;

    private ?TipePenumpang $tipePenumpang;

    private ?DaerahOpersional $asal;

    private ?DaerahOpersional $tujuan;

    private float $tarif;

    /**
     * @return int
     */
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
     * @return TipePenumpang|null
     */
    public function getTipePenumpang(): ?TipePenumpang
    {
        return $this->tipePenumpang;
    }

    /**
     * @param TipePenumpang|null $tipePenumpang
     */
    public function setTipePenumpang(?TipePenumpang $tipePenumpang): self
    {
        $this->tipePenumpang = $tipePenumpang;

        return $this;
    }

    /**
     * @return DaerahOpersional|null
     */
    public function getAsal(): ?DaerahOpersional
    {
        return $this->asal;
    }

    /**
     * @param DaerahOpersional|null $asal
     */
    public function setAsal(?DaerahOpersional $asal): self
    {
        $this->asal = $asal;

        return $this;
    }

    /**
     * @return DaerahOpersional|null
     */
    public function getTujuan(): ?DaerahOpersional
    {
        return $this->tujuan;
    }

    /**
     * @param DaerahOpersional|null $tujuan
     */
    public function setTujuan(?DaerahOpersional $tujuan): self
    {
        $this->tujuan = $tujuan;

        return $this;
    }

    /**
     * @return float
     */
    public function getTarif(): float
    {
        return $this->tarif;
    }

    /**
     * @param float $tarif
     */
    public function setTarif(float $tarif): self
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'kota_asal' => $this->getAsal()->toArray(),
            'kota_tujuan' => $this->getTujuan()->toArray(),
            'tipe_penumpang' => $this->getTipePenumpang()->toArray(),
            'tarif' => $this->getTarif()
        ];
    }
}