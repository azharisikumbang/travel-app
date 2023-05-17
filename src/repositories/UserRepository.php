<?php

require_once __DIR__ . '/../entities/User.php';
require_once __DIR__ . '/BaseRepository.php';

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
            'level' => $user->getRole()
        ]);
    }

    public function get(int $length = 10, int $from = 0) : array
    {
        $query = $this->getDataFromTable($this->table, $length, $from);

        $result = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();
            $user
                ->setId($row['id'])
                ->setNamaLengkap($row['nama_lengkap'])
                ->setKontak($row['kontak'])
                ->setUsername($row['username'])
                ->setPassword($row['password'])
                ->setRole($row['level']);

            $result[] = $user;
        }

        return $result;
    }

    protected function getTable(): string
    {
        return $this->table;
    }
}