<?php

namespace App\ControllerHandler;

use App\Entity\User;
use App\Enum\UserStatusEnum;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Random\RandomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class UserControllerHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public function userEdit(User $user, array $data): User
    {
        $userInfo = $user->getUserInfo();
        $userInfo->setUpdatedAt(new DateTimeImmutable());

        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        $this->userRepository->update($user);

        return $user;
    }

    public function resetPassword(User $user): bool
    {
        $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->getUserStats()->setLastPasswordChangedAt(new DateTimeImmutable());

        $this->userRepository->update($user);

        return true;
    }

    /**
     * @throws RandomException
     */
    public function softDelete(User $user, array $data): bool
    {
        $user->setUsername('Utilisateur supprimé');
        $user->setEmail(Uuid::v4().'@deleted.local');
        $user->setPassword(bin2hex(random_bytes(length: 32)));

        $user->getUserInfo()->setPictureName(null);

        $user->getUserMod()->setStatus(userStatusEnum::SOFT_DELETED);
        $user->getUserMod()->setStatusAt(new DateTimeImmutable());
        $user->getUserMod()->setStatusReason($data['reason']);

        $this->userRepository->update($user);

        return true;
    }

    public function hardDelete(User $user): bool
    {
        $this->userRepository->remove($user);

        return true;
    }
}
