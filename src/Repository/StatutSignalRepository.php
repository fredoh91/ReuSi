<?php

namespace App\Repository;

use App\Entity\StatutSignal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutSignal>
 */
class StatutSignalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutSignal::class);
    }

    public function findOneBySomeIdAndActif($signalId): ?StatutSignal
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.SignalLie = :signalId')
            ->andWhere('s.StatutActif = :actif')
            ->setParameter('signalId', $signalId)
            ->setParameter('actif', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return StatutSignal[] Returns an array of StatutSignal objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StatutSignal
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
