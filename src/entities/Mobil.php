<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class Mobil implements EntityInterface
{
    private int $id;

    private string $merk;

    private string $plat_nomor;

    private int $jumlah_kursi;

    private ?Driver $driver;

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
    public function getMerk(): string
    {
        return $this->merk;
    }

    /**
     * @param string $merk
     */
    public function setMerk(string $merk): self
    {
        $this->merk = $merk;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlatNomor(): string
    {
        return $this->plat_nomor;
    }

    /**
     * @param string $plat_nomor
     */
    public function setPlatNomor(string $plat_nomor): self
    {
        $this->plat_nomor = $plat_nomor;

        return $this;
    }

    /**
     * @return int
     */
    public function getJumlahKursi(): int
    {
        return $this->jumlah_kursi;
    }

    /**
     * @param int $jumlah_kursi
     */
    public function setJumlahKursi(int $jumlah_kursi): self
    {
        $this->jumlah_kursi = $jumlah_kursi;

        return $this;
    }

    /**
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    /**
     * @param Driver|null $driver
     */
    public function setDriver(?Driver $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDisplayName(): string
    {
        return sprintf("%s %s", $this->getMerk(), $this->getPlatNomor());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'merk' => $this->getMerk(),
            'plat_nomor' => $this->getPlatNomor(),
            'jumlah_kursi' => $this->getJumlahKursi(),
            'driver' => $this->getDriver()->toArray(false)
        ];
    }

}