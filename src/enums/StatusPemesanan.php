<?php

enum StatusPemesanan : string
{
    case PENDING = 'PENDING';

    case BATAL = 'BATAL';

    case SELESAI = 'SELESAI';

    public function getColor(): string
    {
        return match ($this) {
            StatusPemesanan::BATAL => 'gray',
            StatusPemesanan::PENDING => 'yellow',
            StatusPemesanan::SELESAI => 'green'
        };
    }

    public static function fromLabel(string $label): StatusPemesanan
    {
        return match ($label) {
            'BATAL' => StatusPemesanan::BATAL,
            'PENDING' => StatusPemesanan::PENDING,
            'SELESAI' => StatusPemesanan::SELESAI
        };
    }
}