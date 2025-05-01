<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function getUserFromUsername(string $username): User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }
}
