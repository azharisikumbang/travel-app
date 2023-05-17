<?php

interface DatabaseInterface
{
    public function getInstance(array $config = []) : \PDO;
}