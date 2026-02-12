<?php

namespace App\ControllerHandler\Admin;

use App\Entity\User;
use App\Repository\RankRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use App\Repository\UserSiteRankRepository;
use DateTimeImmutable;

readonly class UserControllerHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private RankRepository $rankRepository,
        private SiteRepository $siteRepository,
        private UserSiteRankRepository $userSiteRankRepository
    ) {}

    public function userEdit(array $data, string $host): string
    {
        $parts = explode('.', $host);
        $subDomain = $parts[0] ?? null;

        $user = $this->userRepository->findOneBy(['id' => $data['id']]);
        $role = $this->rankRepository->findOneBy(['role' => $data['role']]);
        $site = $this->siteRepository->findOneBy(['name' => ucfirst($subDomain)]);
        $userSiteRank = $this->userSiteRankRepository->findOneBy(['user' => $data['id'], 'site' => $site]);

        $userInfo = $user->getUserInfo();
        $userInfo->setUpdatedAt(new DateTimeImmutable());

        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $userSiteRank->setRole($role);

        if ($data['picture'] === true) {
            $user->getUserInfo()->setPictureName(null);
        }

        $this->userSiteRankRepository->update($userSiteRank);
        $this->userRepository->update($user);


        return true;
    }
}
