<?php

enum Provinsi: int
{
    case SUMATRA_BARAT = 1;

    case RIAU = 2;

    public function getDisplayName(): string
    {
        return match ($this) {
            Provinsi::SUMATRA_BARAT => 'Sumatra Barat',
            Provinsi::RIAU => 'Riau',
        };
    }

    public static function fromLabel(string $label) : false|Provinsi
    {
        $label = strtoupper($label);

        return match($label) {
            'SUMATRA_BARAT' => Provinsi::SUMATRA_BARAT,
            'RIAU' => Provinsi::RIAU,
            default => false
        };
    }

    public static function fromValue(int $label) : false|Provinsi
    {
        return match($label) {
            1 => Provinsi::SUMATRA_BARAT,
            2 => Provinsi::RIAU,
            default => false
        };
    }

    public static function toArray(): array
    {
        return [
            Provinsi::SUMATRA_BARAT->value => Provinsi::SUMATRA_BARAT->getDisplayName(),
            Provinsi::RIAU->value => Provinsi::RIAU->getDisplayName()
        ];
    }
}
