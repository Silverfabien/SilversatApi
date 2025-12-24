<?php

namespace App\ControllerHandler;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserControllerHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    public function userEdit(User $user, array $data): bool
    {
        $userInfo = $user->getUserInfo();
        $userInfo->setFirstname($data['firstname']);
        $userInfo->setLastname($data['lastname']);
        $userInfo->setUpdatedAt(new DateTimeImmutable());

        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        $this->userRepository->update($user);

        return true;
    }

    public function resetPassword(User $user, array $data): bool
    {
        $password = $this->userPasswordHasher->hashPassword($user, $data['password']);
        $user->setPassword($password);

        $this->userRepository->update($user);

        return true;
    }
}
