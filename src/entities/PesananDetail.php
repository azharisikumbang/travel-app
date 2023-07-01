<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class PesananDetail implements EntityInterface
{

    private int $id;

    private int $pesananId;

    private int $nomorKursi;

    private float $hargaTiket;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPesananId(): int
    {
        return $this->pesananId;
    }

    /**
     * @return int
     */
    public function getNomorKursi(): int
    {
        return $this->nomorKursi;
    }

    /**
     * @return float
     */
    public function getHargaTiket(): float
    {
        return $this->hargaTiket;
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
     * @param int $pesananId
     */
    public function setPesananId(int $pesananId): self
    {
        $this->pesananId = $pesananId;

        return $this;
    }

    /**
     * @param int $nomorKursi
     */
    public function setNomorKursi(int $nomorKursi): self
    {
        $this->nomorKursi = $nomorKursi;

        return $this;
    }

    /**
     * @param float $hargaTiket
     */
    public function setHargaTiket(float $hargaTiket): self
    {
        $this->hargaTiket = $hargaTiket;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'pesanan_id' => $this->getPesananId(),
            'nomor_kursi' => $this->getNomorKursi(),
            'harga_tiket' => $this->getHargaTiket()
        ];
    }

}
