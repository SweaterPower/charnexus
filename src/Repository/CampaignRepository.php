<?php

namespace App\Repository;

use App\Entity\Campaign;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
    public function findByAuthor(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
    * @return Campaign[]
    */
    public function findByUserCharacter(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.characters', 'char', Join::ON, 'char.company_id = c.id')
            ->andWhere('char.user = :val')
            ->setParameter('val', $user->getId())
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Campaign[] Returns an array of Campaign objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Campaign
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
