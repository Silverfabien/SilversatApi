<?php

namespace App\Repository;

use App\Entity\UserSiteRank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSiteRank>
 */
class UserSiteRankRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSiteRank::class);
    }

    public function save($userSiteRank): void
    {
        $this->getEntityManager()->persist($userSiteRank);
        $this->getEntityManager()->flush();
    }

    public function update($userSiteRank): void
    {
        $this->getEntityManager()->flush();
    }
}
