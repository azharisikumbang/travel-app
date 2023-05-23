<?php

require_once __DIR__ . '/../Contracts/RouterInterface.php';

class Router implements RouterInterface
{
    private bool $found = false;

    private string $page = "";

    private string $base = __DIR__ . '/../pages';
    public function __construct(private string $paths = "") {}

    public function build(string $path = "") : self
    {
        $this->paths = $path ?? $this->paths;
        $path = rtrim($this->paths, DIRECTORY_SEPARATOR);
        if(false === $this->isPageExists($path)) return $this->makeNotFound();

        $this->found = true;
        $this->page = sprintf("%s/%s.php", $this->base, $path);

        return $this;
    }

    public function getContent() : string
    {
        return $this->page;
    }
    public function makeNotFound(): self
    {
        $page = sprintf("%s/static/404.php", $this->base);
        if(file_exists($page)) $this->page = $page;

        return $this;
    }

    public function isPageExists(string $path) : bool
    {
        return file_exists(sprintf("%s/%s.php", $this->base, $path));
    }

    public function redirectTo(string $route, bool $permanent = false, mixed $data = []) : void
    {
        app()->getManager()->getSessionManager()->add('temp', $data);

        header('Location: ' . site_url($route), true, $permanent ? 301 : 302);

        exit();
    }

    public function getPath() : string
    {
        return $this->paths;
    }
}