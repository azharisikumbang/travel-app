<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class Driver implements EntityInterface
{
    private int $id;

    private string $nama;

    private string $kontak;

    private ?Akun $akun;

    private ?string $photo = null;

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
    public function getNama(): string
    {
        return $this->nama;
    }

    /**
     * @param string $nama
     */
    public function setNama(string $nama): self
    {
        $this->nama = $nama;

        return $this;
    }

    /**
     * @return string
     */
    public function getKontak(): string
    {
        return $this->kontak;
    }

    /**
     * @param string $kontak
     */
    public function setKontak(string $kontak): self
    {
        $this->kontak = $kontak;

        return $this;
    }

    /**
     * @return Akun
     */
    public function getAkun(): ?Akun
    {
        return $this->akun;
    }

    /**
     * @param Akun $akun
     */
    public function setAkun(?Akun $akun): self
    {
        $this->akun = $akun;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     */
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nama' => $this->getNama(),
            'kontak' => $this->getKontak(),
            'akun' => $this->getAkun()?->toArray(),
            'photo' => $this->getPhoto()
        ];
    }

}
