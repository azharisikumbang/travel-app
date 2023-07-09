<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';

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

    protected function basicSave(array $bind) : false|int
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

        return $query->execute($bind) ? $this->getDatabaseConnection()->lastInsertId() : false;

    }

    protected function getByQuery(string $query, array $bind = [], bool $withRelations = false) : array
    {
        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute($bind);

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row, $withRelations);

        return $result;
    }

    public function getDataFromTable(string $table, int $length = 10, int $from = 0, string $order = 'id', string $by = 'DESC'): false|PDOStatement
    {
//        $table = $this->getTable() ?? $table;
        $stmt = $this->getDatabaseConnection()->prepare("SELECT * from $table ORDER BY {$order} {$by} LIMIT {$from}, {$length}");
        $stmt->execute();

        return $stmt;
    }

    public function exists(int|EntityInterface $entity): bool
    {
        return $this->queryExists(
            "SELECT EXISTS(SELECT id FROM {$this->getTable()} WHERE id = :id) as 'exists'",
            ['id' => is_int($entity) ? $entity : $entity->getId()]
        );
    }

    public function queryExists(string $query, array $params) : bool
    {
        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute($params);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC)['exists'];
    }

    public function deleteById(int|EntityInterface $entity) : bool
    {
        $query = "DELETE FROM {$this->getTable()} WHERE id = :id";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        return $stmt->execute(['id' => is_int($entity) ? $entity: $entity->getId()]);
    }

    abstract protected function getTable(): string;

    abstract protected function newEntity(array $row, bool $withRelations = false): EntityInterface;

}