<?php

require_once __DIR__ . '/../entities/User.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function tambahkanAkunOperasional(User $user, UserRepository $repository) : bool
    {
        return $repository->save($user);
    }

    public function listAkunOperasional(int $length = 10, int $from = 0) : array
    {
        return $this->userRepository->get($length, $from);
    }
}