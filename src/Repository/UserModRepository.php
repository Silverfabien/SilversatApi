<?php

namespace App\Repository;

use App\Entity\UserMod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMod>
 */
class UserModRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMod::class);
    }

    public function save($userMod): void
    {
        $this->getEntityManager()->persist($userMod);
        $this->getEntityManager()->flush();
    }
}
