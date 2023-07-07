<?php

require_once __DIR__ . '/../entities/Akun.php';
require_once __DIR__ . '/../repositories/AkunRepository.php';
require_once __DIR__ . '/../enums/Role.php';

class AkunService
{
    private AkunRepository $akunRepository;

    public function __construct()
    {
        $this->akunRepository = new AkunRepository();
    }

    public function tambahkanAkunOperasional(Akun $user, AkunRepository $repository) : bool
    {
        return $repository->save($user);
    }

    public function listAkunOperasional(int $length = 10, int $from = 0) : array
    {
        return $this->akunRepository->get($length, $from);
    }

    public function listDriver(int $length = 10, int $from = 0) : array
    {
        return $this->akunRepository->getByRole(Role::DRIVER, $length, $from);
    }

    public function buatAkunPelanggan(string $nama, string $kontak, string $username, string $password) : false|Akun
    {
        if ($this->cekApakahAkunTerdaftar($username)) return false;

        $user = new Akun();
        $user
            ->setNamaLengkap($nama)
            ->setKontak($kontak)
            ->setUsername($username)
            ->setPassword(password_hash($password, PASSWORD_DEFAULT), true)
            ->setRole(Role::PELANGGAN)
        ;

        return $this->akunRepository->save($user) ? $user : false;
    }

    public function cekApakahAkunTerdaftar(string $username) : bool
    {
        return (bool) $this->akunRepository->findByUsername($username);
    }

    public function usernameTerdaftar(string $username) : ?Akun
    {
        return $this->akunRepository->findByUsername($username);
    }

    public function tambahkanAkunDriver(string $username, string $password) : false|Akun
    {
        return $this->akunRepository->save(
            (new Akun())
                ->setUsername($username)
                ->setRole(Role::DRIVER)
                ->setPassword($password)
        );
    }
}