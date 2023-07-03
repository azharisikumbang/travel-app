<?php

require_once __DIR__ . '/../repositories/UserRepository.php';

class OtentikatorService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function otentikasi(string $username, string $password) : bool
    {
        $user = $this->userRepository->findByUsername($username);


        if(is_null($user)) return false;

        if (false === $this->verify($user, $password)) return false;

        $this->createAuthenticatedSession($user);

        return true;
    }

    private function verify(User $user, string $plainPassword) : bool
    {
        return password_verify($plainPassword, $user->getPassword());
    }

    private function createAuthenticatedSession(User $user) : void
    {
        session()->add('auth', $user->toArray());
    }

}
