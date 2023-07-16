<?php

class Paginator
{
    private array $data;

    private int $page;

    public function __construct(array $data, int $page = 1)
    {
        $this->data = $data;
        $this->page = $page;
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'total' => count($this->data),
            'data' => $this->data
        ];
    }
}