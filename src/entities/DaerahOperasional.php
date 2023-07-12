<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
require_once __DIR__ . '/../enums/Provinsi.php';

class DaerahOperasional implements EntityInterface
{
    private int $id;

    private string $nama_kota;

    private Provinsi $provinsi;
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
     * @return string
     */
    public function getNamaKota(): string
    {
        return $this->nama_kota;
    }

    /**
     * @param string $nama_kota
     */
    public function setNamaKota(string $nama_kota): self
    {
        $this->nama_kota = $nama_kota;

        return $this;
    }

    /**
     * @return Provinsi
     */
    public function getProvinsi(): Provinsi
    {
        return $this->provinsi;
    }

    /**
     * @param Provinsi $provinsi
     */
    public function setProvinsi(int|Provinsi $provinsi): self
    {
        $this->provinsi = (is_int($provinsi)) ? Provinsi::fromValue($provinsi) : $provinsi;

        return $this;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'nama_kota' => $this->getNamaKota(),
            'provinsi' => [
                'id' => $this->getProvinsi()->value,
                'nama' => $this->getProvinsi()->getDisplayName()
            ]
        ];
    }
}