<?php

namespace App\Repository;

use App\Entity\Newsletter;
use App\Repository\Abstract\AbstractEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Newsletter>
 */
class NewsletterRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    //    /**
    //     * @return Newsletter[] Returns an array of Newsletter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findById($id): ?Newsletter
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function add(Newsletter $newsletter): void 
    {
        $this->getEntityManager()->persist($newsletter);
        $this->flush();
    }

    public function remove(Newsletter $newsletter): void 
    {
        $this->getEntityManager()->remove($newsletter);
        $this->flush();
    }
}
