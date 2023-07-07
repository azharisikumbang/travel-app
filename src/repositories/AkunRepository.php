<?php

require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../enums/Role.php';

class AkunRepository extends BaseRepository
{
    private string $table = "m_akun";

    public function getTableName() : string
    {
        return $this->table;
    }
    public function save(Akun $akun) : false|Akun
    {
        $saved = $this->basicSave([
            'username' => $akun->getUsername(),
            'password' => $akun->getPassword(),
            'role' => $akun->getRole()->value
        ]);

        return ($saved) ? $akun->setId($saved) : false;
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $query = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row, true);

        return $result;
    }

    public function getByRole(Role $role, int $length = 10, int $from = 0): array
    {
        $query = "SELECT * FROM {$this->getTable()} WHERE role = :role LIMIT $from, $length";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['role' => $role->value]);

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row, true);

        return $result;
    }

    public function findById(int $driver) : ?Akun
    {
        $row = $this->basicFindById($driver);

        return ($row) ? $this->newEntity($row) : null;
    }

    public function findByUsername(string $username) : ?Akun
    {
        $query = "SELECT * FROM {$this->getTable()} WHERE username = :username";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['username' => $username]);

        if($stmt->rowCount() < 1) return null;

        return $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC), true);
    }

    protected function newEntity(array $row, bool $hashed = false) : Akun
    {
        return (new Akun())
            ->setId($row['id'])
            ->setUsername($row['username'])
            ->setPassword($row['password'], $hashed)
            ->setRole(Role::fromLabel($row['role']));
    }

    protected function getTable(): string
    {
        return $this->table;
    }
}