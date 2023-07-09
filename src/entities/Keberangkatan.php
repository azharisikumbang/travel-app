<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class Keberangkatan implements EntityInterface
{
    private int $id;

    private null|int|Rute $rute;

    private null|int|Mobil $mobil;

    private null|int|JamKeberangkatan $jamKeberangkatan;

    private null|int|DateTimeInterface $lastUpdated;

    public function getId(): int
    {
       return  $this->id;
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
     * @return Rute
     */
    public function getRute(): Rute
    {
        return $this->rute;
    }

    /**
     * @param Rute $rute
     */
    public function setRute(null|int|Rute $rute): self
    {
        $this->rute = $rute;

        return $this;
    }

    /**
     * @return Mobil
     */
    public function getMobil(): null|int|Mobil
    {
        return $this->mobil;
    }

    /**
     * @param Mobil $mobil
     */
    public function setMobil(null|int|Mobil $mobil): self
    {
        $this->mobil = $mobil;

        return $this;
    }

    /**
     * @return JamKeberangkatan
     */
    public function getJamKeberangkatan(): null|int|JamKeberangkatan
    {
        return $this->jamKeberangkatan;
    }

    /**
     * @param JamKeberangkatan $jamKeberangkatan
     */
    public function setJamKeberangkatan(null|int|JamKeberangkatan $jamKeberangkatan): self
    {
        $this->jamKeberangkatan = $jamKeberangkatan;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastUpdated(): ?DateTimeInterface
    {
        return $this->lastUpdated;
    }

    /**
     * @param DateTimeInterface|null $lastUpdated
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
            'rute' => $this->getRute()->toArray(),
            'mobil' => $this->getMobil()?->toArray(),
            'jam_keberangkatan' => $this->getJamKeberangkatan()?->toArray(),
            'last_updated' => $this->getLastUpdated()?->format('Y-m-d H:i:s')
        ];
    }

}