<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class DaerahOpersional implements EntityInterface
{
    private int $id;

    private string $nama_kota;

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


}