<?php

require_once __DIR__ . '/enums/Role.php';
require_once __DIR__ . '/Contracts/ManagerInterface.php';

final class App
{
    private array $config = [];
    private ?ManagerInterface $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function setTimeZone(string $zone): void
    {
        date_default_timezone_set($zone);
    }

    public function getManager() : ManagerInterface
    {
        return $this->manager;
    }

    public function buildRoute(string $paths = "") : self
    {
        $this->getManager()->getRouterManager()->build($paths);

        return $this;
    }

    public function getContent() : string
    {
        return $this->getManager()->getRouterManager()->getContent();
    }

    public function loadTemplateFor(?string $for, mixed $content = null) : void
    {
        $content = $content ?? $this->getContent();
        $role = match (strtolower($for)) {
            'admin' => Role::ADMIN,
            'pelanggan' => Role::PELANGGAN,
            'driver' => Role::DRIVER,
            default => Role::PUBLIC
        };

        $pageTemplate = sprintf("%s/templates/%s.php", __DIR__, $role->pageTemplate());

        if(file_exists($pageTemplate)) require_once $pageTemplate;
    }

    public function loadFunction(string $name, \Closure $callback = null)
    {
        $functionName = "";
        if (file_exists(sprintf("%s/%s.php", __DIR__, $name)))
            $functionName = sprintf("%s/%s.php", __DIR__, $name);

        if (file_exists(sprintf("%s/functions/%s.php", __DIR__, $name)))
            $functionName = sprintf("%s/functions/%s.php", __DIR__, $name);

        if (is_callable($callback)) return $callback($functionName);

        require_once $functionName;
    }

    public function addConfigFor(string $container, mixed $value) : void
    {
        $this->config[$container] = $value;
    }

    public function getConfigFrom(string $container, string $key) : mixed
    {
        return $this->config[$container][$key] ?? null;
    }

    public function setEnvironment(string $env = "") : self
    {
        $env = strtolower($env ?? $this->getConfigFrom('app', 'env'));

        if ($env == 'prod' || $env == 'production' || $env == 'prods') {
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
            error_reporting(E_ERROR | E_WARNING | E_PARSE);

            return $this;
        }

        error_reporting(E_ALL);
        error_reporting(-1);
        ini_set('error_reporting', E_ALL);

        return $this;
    }

    public function run(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $paths = explode("/", $this->getManager()->getRouterManager()->getPath());

        if(($method == 'GET') && (strtolower($paths[0]) != 'api')) {
            $template = 'public';

            $sessionUser = session()->auth();
            if($sessionUser) $template = $sessionUser->getRole()->name;

            $this->loadTemplateFor($template);
        } else {
            require_once $this->getContent();
        }

        $this->getManager()->getSessionManager()->remove('temp');
    }
}