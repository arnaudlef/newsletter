<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\SubscriptionStatus;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
    * @return Subscription[] Returns an array of Subscription objects
    */
    public function findApprovedByNewsletter($newsletter): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.newsletter = :newsletter')
            ->setParameter('newsletter', $newsletter)
            ->andWhere(`s.status = 'accepted'`)
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findOneBySomeField($value): ?Subscription
    // {
    //     return $this->createQueryBuilder('s')
    //         ->andWhere('s.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
