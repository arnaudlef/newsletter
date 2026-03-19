<?php

namespace App\Repository\Abstract;

use App\Repository\Abstract\AbstractEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository
 */
abstract class AbstractEntityRepository extends ServiceEntityRepository implements AbstractEntityRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
    ) {
        parent::__construct($registry, $entityClass);
    }

    public function flush(): void 
    {
        $this->getEntityManager()->flush();
    }
}