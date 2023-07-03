<?php

enum Role
{
    case PUBLIC;
    case PELANGGAN;
    case ADMIN;
    case DRIVER;

    public function pageTemplate(): string
    {
        return match($this) {
            Role::PUBLIC => 'public',
            Role::PELANGGAN => 'pelanggan',
            Role::ADMIN => 'admin',
            Role::DRIVER => 'driver'
        };
    }

    public function redirectPage(): string
    {
        return match($this) {
            Role::PUBLIC => 'login',
            Role::PELANGGAN => 'pelanggan',
            Role::ADMIN => 'admin',
            Role::DRIVER => 'driver'
        };
    }

    public static function fromLabel(string $label): Role
    {
        return match(strtolower($label)) {
            'driver' => Role::DRIVER,
            'pelanggan' => Role::PELANGGAN,
            'admin' => Role::ADMIN,
            default => Role::PUBLIC
        };
    }

    public function isAdmin() : bool
    {
        return match($this) {
            Role::ADMIN => true,
            default => false
        };
    }
}