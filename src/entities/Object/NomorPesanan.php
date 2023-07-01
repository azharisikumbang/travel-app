<?php

final class NomorPesanan
{
    public function __construct(private string $nomorPesanan, private int $iterasi)
    {
    }

    /**
     * @return string
     */
    public function getNomorPesanan(): string
    {
        return $this->nomorPesanan;
    }

    /**
     * @param string $nomorPesanan
     */
    public function setNomorPesanan(string $nomorPesanan): self
    {
        $this->nomorPesanan = $nomorPesanan;

        return $this;
    }

    /**
     * @return int
     */
    public function getIterasi(): int
    {
        return $this->iterasi;
    }

    /**
     * @param int $iterasi
     */
    public function setIterasi(int $iterasi): self
    {
        $this->iterasi = $iterasi;

        return $this;
    }

    public function toArray() : array
    {
        return [
          'nomor_pesanan' => $this->getNomorPesanan(),
          'iterasi' => $this->getIterasi()
        ];
    }
}
