<?php

require_once __DIR__ . '/../Contracts/EntityInterface.php';
class User implements EntityInterface
{
    private int $id;

    private string $namaLengkap;

    private string $username;

    private string $password;

    private string $kontak;

    private string $role = 'PELANGGAN';

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
    public function getNamaLengkap(): string
    {
        return $this->namaLengkap;
    }

    /**
     * @param string $namaLengkap
     */
    public function setNamaLengkap(string $namaLengkap): self
    {
        $this->namaLengkap = $namaLengkap;

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
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * @return string
     */
    public function getKontak(): string
    {
        return $this->kontak;
    }

    /**
     * @param string $kontak
     */
    public function setKontak(string $kontak): self
    {
        $this->kontak = $kontak;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): self
    {
        if(!in_array(strtoupper($role), ['PELANGGAN', 'DRIVER', 'ADMINISTRATOR'])) $role = 'PELANGGAN';
        $this->role = $role;

        return $this;
    }

}