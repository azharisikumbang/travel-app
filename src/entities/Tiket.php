<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class Tiket implements EntityInterface
{
    private int $id;

    private KategoriPelanggan $kategori;

    private Rute $rute;

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
     * @return KategoriPelanggan|null
     */
    public function getKategoriPelanggan(): KategoriPelanggan
    {
        return $this->kategori;
    }

    /**
     * @param KategoriPelanggan|null $kategori
     */
    public function setKategoriPelanggan(KategoriPelanggan $kategori): self
    {
        $this->kategori = $kategori;

        return $this;
    }

    /**
     * @return DaerahOperasional|null
     */
    public function getRute(): Rute
    {
        return $this->rute;
    }

    /**
     * @param DaerahOperasional|null $rute
     */
    public function setRute(Rute $rute): self
    {
        $this->rute = $rute;

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
            'rute' => $this->getRute()->toArray(),
            'kategori' => $this->getKategoriPelanggan()->toArray(),
            'tarif' => $this->getTarif()
        ];
    }
}