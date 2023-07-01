<?php

require_once __DIR__ . '/../Contracts/SessionInterface.php';
class Session implements SessionInterface
{
    public function __construct()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    public function all() : array
    {
        return $_SESSION ?? [];
    }

    public function add(string $key, mixed $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key) : mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key) : void
    {
        unset($_SESSION[$key]);
    }

    public function destroy() : void
    {
        session_destroy();
    }

    public function exists(string $key) : bool
    {
        return isset($_SESSION[$key]);
    }
}