<?php

class Request
{
    public function __construct(readonly array $data)
    {

    }

    public function isPostRequest() : bool
    {
        return strtolower($_SERVER['REQUEST_METHOD'])  === 'post';
    }

    public function notPostRequest() : bool
    {
        return !$this->isPostRequest();
    }

    public function isGetRequest() : bool
    {
        return strtolower($_SERVER['REQUEST_METHOD']) === 'get';
    }

    public function notGetRequest() : bool
    {
        return !$this->isGetRequest();
    }

    public function isAuthenticated() : bool
    {
        return (bool) session()->auth();
    }

    public function isAuthenticatedAs(string|Role $role): bool
    {
        return session()->isAuthenticatedAs($role);
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function has(mixed $searched) : bool
    {
        $searched = (!is_array($searched)) ? [$searched] : $searched;

        foreach ($searched as $search) {
            if (!in_array($search, array_keys($this->getData()))) return false;
        }

        return true;
    }
}