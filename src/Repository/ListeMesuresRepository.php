<?php

namespace App\Repository;

use App\Entity\ListeMesures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<ListeMesures>
 */
class ListeMesuresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListeMesures::class);
    }
    /**
     * @return QueryBuilder
     */
    public function findActiveSortedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('lm')
            ->andWhere('lm.DesactivateAt IS NULL')
            ->orderBy('lm.OrdreTriListe', 'ASC');
    }
    //    /**
    //     * @return ListeMesures[] Returns an array of ListeMesures objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ListeMesures
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
