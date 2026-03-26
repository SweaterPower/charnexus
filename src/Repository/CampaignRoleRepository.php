<?php

namespace App\Repository;

use App\Entity\CampaignRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampaignRole>
 */
class CampaignRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampaignRole::class);
    }

    public function findOneById(int $userId, int $campaignId): ?CampaignRole
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->andWhere('r.campaign = :campaignId')
            ->setParameter('userId', $userId)
            ->setParameter('campaignId', $campaignId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
