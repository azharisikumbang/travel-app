<?php

abstract class BaseRepository
{
    public function __construct(private ?PDO $db = null)
    {
    }

    protected function getDatabaseConnection() : PDO
    {
        return $this->db ?? app()->getManager()->getDatabaseManager()->getInstance();
    }

    public function basicFindById(int $id) : mixed
    {
        $stmt = $this->getDatabaseConnection()->prepare("SELECT * from {$this->getTable()} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function basicSave(array $bind) : bool
    {
        $bindKeys = [];
        foreach ($bind as $attr => $value)
        {
            $bindKeys[] = ":" . $attr;
        }

        $valueKeys = implode(", ", array_keys($bind));
        $bindKeys = implode(", ", $bindKeys);

        $query = $this
            ->getDatabaseConnection()
            ->prepare("INSERT INTO {$this->getTable()} ($valueKeys) VALUES({$bindKeys})");

        return $query->execute($bind);

    }

    public function getDataFromTable(string $table, int $length = 10, int $from = 0): false|PDOStatement
    {
//        $table = $this->getTable() ?? $table;
        $stmt = $this->getDatabaseConnection()->prepare("SELECT * from $table ORDER BY id DESC LIMIT {$from}, {$length}");
        $stmt->execute();

        return $stmt;
    }

    abstract protected function getTable(): string;

}