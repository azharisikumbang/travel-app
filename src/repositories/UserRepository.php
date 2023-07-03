<?php

require_once __DIR__ . '/../entities/User.php';
require_once __DIR__ . '/BaseRepository.php';
require_once __DIR__ . '/../enums/Role.php';

class UserRepository extends BaseRepository
{
    private string $table = "users";

    public function getTableName() : string
    {
        return $this->table;
    }
    public function save(User $user) : bool
    {
        $query = $this
            ->getDatabaseConnection()
            ->prepare("INSERT INTO $this->table (nama_lengkap, username, password, kontak, level) VALUES(:nama_lengkap, :username, :password, :kontak, :level)");

        return $query->execute([
            'nama_lengkap' => $user->getNamaLengkap(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'kontak' => $user->getKontak(),
            'level' => $user->getRole()->value
        ]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $query = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) $result[] = $this->newEntity($row, true);

        return $result;
    }

    public function findByUsername(string $username) : ?User
    {
        $query = "SELECT * FROM {$this->getTable()} WHERE username = :username";

        $stmt = $this->getDatabaseConnection()->prepare($query);
        $stmt->execute(['username' => $username]);

        if($stmt->rowCount() < 1) return null;

        return $this->newEntity($stmt->fetch(PDO::FETCH_ASSOC), true);
    }

    protected function newEntity(array $row, bool $hashed = false) : User
    {
        return (new User())
            ->setId($row['id'])
            ->setNamaLengkap($row['nama_lengkap'])
            ->setKontak($row['kontak'])
            ->setUsername($row['username'])
            ->setPassword($row['password'], $hashed)
            ->setRole(Role::fromLabel($row['level']));
    }

    protected function getTable(): string
    {
        return $this->table;
    }
}