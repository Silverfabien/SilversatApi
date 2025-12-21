<?php

namespace App\ControllerHandler;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Entity\UserMod;
use App\Entity\UserSecurity;
use App\Entity\UserSiteRank;
use App\Entity\UserStats;
use App\Repository\RankRepository;
use App\Repository\SiteRepository;
use App\Repository\UserInfoRepository;
use App\Repository\UserModRepository;
use App\Repository\UserRepository;
use App\Repository\UserSecurityRepository;
use App\Repository\UserSiteRankRepository;
use App\Repository\UserStatsRepository;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class SecurityControllerHandler
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
        private SiteRepository $siteRepository,
        private RankRepository $rankRepository,
        private UserSiteRankRepository $userSiteRankRepository,
        private UserInfoRepository $userInfoRepository,
        private UserModRepository $userModRepository,
        private UserSecurityRepository $userSecurityRepository,
        private UserStatsRepository $userStatsRepository,
    ) {}

    public function createUser(User $user, string $url): bool
    {
        // Create User
        $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);

        // Create UserSiteRank
        $allSites = $this->siteRepository->findAll();
        $defaultRank = $this->rankRepository->findOneBy(['role' => 'ROLE_USER']);
        foreach ($allSites as $site) {
            $userSiteRank = new UserSiteRank();
            $userSiteRank->setSite($site);
            $userSiteRank->setUser($user);
            $userSiteRank->setRole($defaultRank);

            $this->userSiteRankRepository->save($userSiteRank);
        }

        $this->userRepository->save($user);

        // Create UserInfo
        $userInfo = new UserInfo();
        $userInfo->setUser($user);

        $this->userInfoRepository->save($userInfo);

        // Create UserMod
        $userMod = new UserMod();
        $userMod->setUser($user);

        $this->userModRepository->save($userMod);

        // Create UserSecurity
        $userSecurity = new UserSecurity();
        $userSecurity->setUser($user);

        $this->userSecurityRepository->save($userSecurity);

        // Create UserStats
        $userStats = new UserStats();
        $userStats->setUser($user);
        $userStats->setRegisterOn($url);

        $this->userStatsRepository->save($userStats);

        return true;
    }

    public function verifyAccount(UserSecurity $userSecurity): bool
    {
        $userSecurity->getUser()->setIsVerify(true);
        $userSecurity->getUser()->setIsVerifyAt(new DateTimeImmutable());
        $userSecurity->setConfirmationToken(null);
        $userSecurity->setConfirmationTokenExpirationAt(null);

        $this->userRepository->update($userSecurity);

        return true;
    }

    public function deleteAccount(UserSecurity $userSecurity): bool
    {
        $this->userRepository->remove($userSecurity->getUser());

        return true;
    }

    public function sendMail(User $user): bool
    {
        $search = $this->userSecurityRepository->findOneBy(['user' => $user->getId()]);

        $token = Uuid::v4()->toRfc4122();
        $search->setConfirmationToken($token);
        $search->setConfirmationTokenExpirationAt(new DateTimeImmutable('+30 minutes'));

        $this->userRepository->update($search);

        return true;
    }

    public function forgotPassword(User $user): bool
    {
        $token = Uuid::v4()->toRfc4122();
        $user->getUserSecurity()->setResetPasswordToken($token);
        $user->getUserSecurity()->setResetPasswordTokenExpirationAt(new DateTimeImmutable('+30 minutes'));

        $this->userRepository->update($user);

        return true;
    }

    public function resetForgotPassword(User $user): bool
    {
        $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->getUserSecurity()->setResetPasswordToken(null);
        $user->getUserSecurity()->setResetPasswordTokenExpirationAt(null);

        $this->userRepository->update($user);

        return true;
    }
}
