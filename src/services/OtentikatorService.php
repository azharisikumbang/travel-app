<?php

require_once __DIR__ . '/../repositories/AkunRepository.php';

class OtentikatorService
{
    private AkunRepository $akunRepository;

    public function __construct()
    {
        $this->akunRepository = new AkunRepository();
    }

    public function otentikasi(string $username, string $password) : bool
    {
        $user = $this->akunRepository->findByUsername($username);

        if(is_null($user)) return false;

        if (false === $this->verify($user, $password)) return false;

        $this->createAuthenticatedSession($user);

        return true;
    }

    private function verify(Akun $user, string $plainPassword) : bool
    {
        return password_verify($plainPassword, $user->getPassword());
    }

    private function createAuthenticatedSession(Akun $user) : void
    {
        session()->add('auth', $user->toArray());
    }

}
