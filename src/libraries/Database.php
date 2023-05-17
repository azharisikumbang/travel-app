<?php

require_once __DIR__ . '/../Contracts/DatabaseInterface.php';

final class Database implements DatabaseInterface
{

    protected null|\PDO $instance;

    public function __construct(private array $config = [])
    {
    }

    public function getInstance(array $config = []) : \PDO
    {
        if (false === empty($this->instance)) return $this->instance;

        if(false === empty($config)) $this->config = $config;

        $config = $this->config ?? [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'database',
            'port' => '3306'
        ];

        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s",
                $config['host'],
                $config['port'],
                $config['database']
            );
            $this->instance = new PDO($dsn, $config['username'], $config['password']);
            $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this->instance;
    }
}
