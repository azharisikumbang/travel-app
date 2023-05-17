<?php

interface SessionInterface
{
    public function all() : array;

    public function get(string $key) : mixed;

    public function add(string $key, mixed $value): void;

    public function remove(string $key) : void;

    public function destroy() : void;

    public function exists(string $key): bool;
}