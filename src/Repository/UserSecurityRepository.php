<?php

namespace App\Repository;

use App\Entity\UserSecurity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSecurity>
 */
class UserSecurityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSecurity::class);
    }

    public function save($userSecurity): void
    {
        $this->getEntityManager()->persist($userSecurity);
        $this->getEntityManager()->flush();
    }
}
