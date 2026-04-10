<?php

namespace App\Repository;

use App\Dictionary\CampaignRoleDictionary;
use App\Entity\Campaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campaign>
 */
class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    /**
    * @return Campaign[]
    */
    public function findByAuthor(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
    * @return Campaign[]
    */
    public function findByUserCharacter(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.roles', 'r', 'WITH', 'r.campaign = c.id AND r.user = :userId')
            ->andWhere('r.role != :roleGM AND r.role != :roleBlocked')
            ->setParameter('userId', $userId)
            ->setParameter('roleGM', CampaignRoleDictionary::ROLE_GAME_MASTER)
            ->setParameter('roleBlocked', CampaignRoleDictionary::ROLE_BLOCKED)
            ->getQuery()
            ->getResult();
    }
}
