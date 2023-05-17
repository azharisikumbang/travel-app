<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

class TipePenumpang implements EntityInterface
{
    private int $id;

    private string $tipe_penumpang;

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
    public function getTipePenumpang(): string
    {
        return $this->tipe_penumpang;
    }

    /**
     * @param string $tipe_penumpang
     */
    public function setTipePenumpang(string $tipe_penumpang): self
    {
        $this->tipe_penumpang = $tipe_penumpang;

        return $this;
    }
}