<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class KategoriPelanggan implements EntityInterface
{
    private int $id;

    private string $kategori;

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
    public function getKategori(): string
    {
        return $this->kategori;
    }

    /**
     * @param string $kategori
     */
    public function setKategori(string $kategori): self
    {
        $this->kategori = $kategori;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'kategori' => $this->getKategori()
        ];
    }
}