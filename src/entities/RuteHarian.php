<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class RuteHarian implements EntityInterface
{
    private int $id;

    private DaerahOpersional $asal;

    private DaerahOpersional $tujuan;

    private ?Mobil $mobil;

    private ?JamKeberangkatan $jamKeberangkatan;

    private ?DateTimeInterface $lastUpdated;

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
     * @return DaerahOpersional
     */
    public function getAsal(): DaerahOpersional
    {
        return $this->asal;
    }

    /**
     * @param DaerahOpersional $asal
     */
    public function setAsal(DaerahOpersional $asal): self
    {
        $this->asal = $asal;

        return $this;
    }

    /**
     * @return DaerahOpersional
     */
    public function getTujuan(): DaerahOpersional
    {
        return $this->tujuan;
    }

    /**
     * @param DaerahOpersional $tujuan
     */
    public function setTujuan(DaerahOpersional $tujuan): self
    {
        $this->tujuan = $tujuan;

        return $this;
    }

    /**
     * @return Mobil
     */
    public function getMobil(): ?Mobil
    {
        return $this->mobil;
    }

    /**
     * @param Mobil $mobil
     */
    public function setMobil(?Mobil $mobil): self
    {
        $this->mobil = $mobil;

        return $this;
    }

    /**
     * @return JamKeberangkatan
     */
    public function getJamKeberangkatan(): ?JamKeberangkatan
    {
        return $this->jamKeberangkatan;
    }

    /**
     * @param JamKeberangkatan $jamKeberangkatan
     */
    public function setJamKeberangkatan(?JamKeberangkatan $jamKeberangkatan): self
    {
        $this->jamKeberangkatan = $jamKeberangkatan;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getLastUpdated(): ?DateTimeInterface
    {
        return $this->lastUpdated;
    }

    /**
     * @param DateTimeInterface $lastUpdated
     */
    public function setLastUpdated(?DateTimeInterface $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'asal' => $this->getAsal()->toArray(),
            'tujuan' => $this->getTujuan()->toArray(),
            'jam_keberangkatan' => $this->getJamKeberangkatan()?->toArray(),
            'mobil' => $this->getMobil()?->toArray(),
            'last_updated' => $this->getLastUpdated()?->format('Y-m-d H:i:s')
        ];
    }

}
