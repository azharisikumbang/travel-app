<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
require_once __DIR__ . '/../enums/Role.php';

class Akun implements EntityInterface
{
    private int $id;

    private string $username;

    private string $password;

    private Role $role;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password, bool $hashed = false): self
    {
        $this->password = $password;
        if (!$hashed) $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string|Role $role): self
    {
        $this->role = is_string($role) ? Role::fromLabel($role): $role;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'role' => $this->getRole()->value,
            'username' => $this->getUsername()
        ];
    }
}