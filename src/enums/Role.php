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
}