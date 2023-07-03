<?php

enum StatusBuktiPembayaran: string
{
    case PENDING = 'PENDING';
    case VALID = 'VALID';
    case INVALID = 'INVALID';
    case UNCONFIRMED = 'UNCONFIRMED';

    public function getColor(): string
    {
        return match ($this) {
            StatusBuktiPembayaran::INVALID => 'red',
            StatusBuktiPembayaran::PENDING => 'gray',
            StatusBuktiPembayaran::VALID => 'green',
            StatusBuktiPembayaran::UNCONFIRMED => 'yellow',
        };
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            StatusBuktiPembayaran::INVALID => 'DITOLAK',
            StatusBuktiPembayaran::PENDING => 'MENUNGGU PEMBAYARAN',
            StatusBuktiPembayaran::VALID => 'DITERIMA',
            StatusBuktiPembayaran::UNCONFIRMED => 'MENUNGGU KONFIRMASI',
        };
    }

    public static function fromLabel(string $label) : StatusBuktiPembayaran
    {
        $label = strtoupper($label);

        return match($label) {
            'PENDING' => StatusBuktiPembayaran::PENDING,
            'INVALID' => StatusBuktiPembayaran::INVALID,
            'VALID' => StatusBuktiPembayaran::VALID,
            'UNCONFIRMED' => StatusBuktiPembayaran::UNCONFIRMED
        };
    }
}
