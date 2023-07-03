<?php

class Manager implements ManagerInterface
{

    private ?DatabaseInterface $databaseManager;

    private ?SessionInterface $sessionManager;

    private ?RouterInterface $routerManager;

    private ?Response $responseManager;


    public function setDatabaseManager(\DatabaseInterface $manager) : self
    {
        $this->databaseManager = $manager;

        return $this;
    }

    /**
     * @return DatabaseInterface|null
     */
    public function getDatabaseManager(): ?DatabaseInterface
    {
        return $this->databaseManager;
    }

    /**
     * @param SessionInterface|null $sessionManager
     */
    public function setSessionManager(?SessionInterface $sessionManager): self
    {
        $this->sessionManager = $sessionManager;

        return $this;
    }

    public function getSessionManager(): ?SessionInterface
    {
        return $this->sessionManager;
    }

    /**
     * @param Response|null $responseManager
     */
    public function setResponseManager(?Response $responseManager): self
    {
        $this->responseManager = $responseManager;

        return $this;
    }

    public function getResponseManager(): ?Response
    {
        return $this->responseManager;
    }

    /**
     * @return RouterInterface|null
     */
    public function getRouterManager(): ?RouterInterface
    {
        return $this->routerManager;
    }

    /**
     * @param RouterInterface|null $routerManager
     */
    public function setRouterManager(?RouterInterface $routerManager): self
    {
        $this->routerManager = $routerManager;

        return $this;
    }

    public function getEntity(string $name) : mixed
    {
        $filename = sprintf("%s/entities/%s.php", __DIR__, $name);
        if(file_exists($filename)) {
            require_once $filename;

            return new $name;
        }

        return null;
    }

        public function getService(string $name) : mixed
    {
        $filename = sprintf("%s/services/%s.php", __DIR__, $name);
        if(file_exists($filename)) {
            require_once $filename;

            return new $name;
        }

        return null;
    }

    public function getRepository(string $name) : mixed
    {
        $filename = sprintf("%s/repositories/%s.php", __DIR__, $name);
        if(file_exists($filename)) {
            require_once $filename;

            return new $name;
        }

        return null;
    }

}