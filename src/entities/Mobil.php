<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class Mobil implements EntityInterface
{
    private int $id;

    private string $merk;

    private string $nomor_polisi;

    private int $jumlah_kursi;

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
    public function getNomorPolisi(): string
    {
        return $this->nomor_polisi;
    }

    /**
     * @param string $nomor_polisi
     */
    public function setNomorPolisi(string $nomor_polisi): self
    {
        $this->nomor_polisi = $nomor_polisi;

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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'merk' => $this->getMerk(),
            'nomor_polisi' => $this->getNomorPolisi(),
            'jumlah_kursi' => $this->getJumlahKursi()
        ];
    }

}