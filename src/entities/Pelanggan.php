<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
require_once __DIR__ .'/KategoriPelanggan.php';
require_once __DIR__ . '/Akun.php';

class Pelanggan implements EntityInterface
{
    private int $id;

    private string $nama;

    private ?string $kontak = null;

    private ?string $photo= null;

    private KategoriPelanggan $kategoriPelanggan;

    private ?string $photoIdentitas= null;

    private Akun $akun;

    public function __construct(string $nama = "")
    {
        $this->nama = $nama;
    }

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
     * @return string|null
     */
    public function getKontak(): ?string
    {
        return $this->kontak;
    }

    /**
     * @param string|null $kontak
     */
    public function setKontak(?string $kontak): self
    {
        $this->kontak = $kontak;

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

    /**
     * @return KategoriPelanggan
     */
    public function getKategoriPelanggan(): KategoriPelanggan
    {
        return $this->kategoriPelanggan;
    }

    /**
     * @param KategoriPelanggan $kategoriPelanggan
     */
    public function setKategoriPelanggan(KategoriPelanggan $kategoriPelanggan): self
    {
        $this->kategoriPelanggan = $kategoriPelanggan;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhotoIdentitas(): ?string
    {
        return $this->photoIdentitas;
    }

    /**
     * @param string|null $photoIdentitas
     */
    public function setPhotoIdentitas(?string $photoIdentitas): self
    {
        $this->photoIdentitas = $photoIdentitas;

        return $this;
    }

    /**
     * @return Akun
     */
    public function getAkun(): Akun
    {
        return $this->akun;
    }

    /**
     * @param Akun $akun
     */
    public function setAkun(Akun $akun): self
    {
        $this->akun = $akun;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nama' => $this->getNama(),
            'kontak' => $this->getKontak(),
            'photo' => $this->getPhoto(),
            'photo_identitias' => $this->getPhotoIdentitas(),
            'kategori_pelanggan' => $this->getKategoriPelanggan()->toArray(),
            'akun' => $this->getAkun()->toArray()
        ];
    }

}