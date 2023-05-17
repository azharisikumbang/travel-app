<?php

interface ManagerInterface
{
    public function getDatabaseManager() : ?DatabaseInterface;

    public function getSessionManager() : ?SessionInterface;

    public function getRouterManager(): ?RouterInterface;
}