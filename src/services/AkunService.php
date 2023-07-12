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

    public function buatAkunBaru(Akun $user) : false|Akun
    {
        return $this->akunRepository->save($user);
    }

    public function listAkunOperasional(int $length = 10, int $from = 0) : array
    {
        return $this->akunRepository->get($length, $from);
    }

    public function listDriver(int $length = 10, int $from = 0) : array
    {
        return $this->akunRepository->getByRole(Role::DRIVER, $length, $from);
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

    public function informasiAkun(string $username) : null|Akun
    {
        return $this->akunRepository->findByUsername($username);
    }

    public function gantiPasswordAkun(string $username, string $password, bool $sameAsAuthenticated = false) : bool
    {
        if ($sameAsAuthenticated){
            if ($username !== session()->auth()->getUsername()) return false;
        }

        /** @var $akun Akun */
        $akun = session()->auth();
        $akun->setPassword($password);

        return $this->akunRepository->updatePassword($akun);
    }
}