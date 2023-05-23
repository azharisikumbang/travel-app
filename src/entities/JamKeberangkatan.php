<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class JamKeberangkatan implements EntityInterface
{
    private int $id;

    private string $jam;

    private string $alias;

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
    public function getJam(bool $second = false): string
    {
        return ($second) ? $this->jam : rtrim(rtrim($this->jam, "00"), ":");
    }

    /**
     * @param string $jam
     */
    public function setJam(string $jam): self
    {
        $this->jam = $jam;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'jam' => $this->getJam(),
            'full_jam' => $this->jam,
            'alias' => $this->getAlias(),
        ];
    }

}