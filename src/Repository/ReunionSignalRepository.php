<?php

namespace App\Repository;

use App\Entity\ReunionSignal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReunionSignal>
 */
class ReunionSignalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReunionSignal::class);
    }


    /**
     * Retourne la liste des "réunions signal" qui ne sont pas annulés.
     *
     * @param integer $days - Nombre de jours à considérer, au dela de ce nombre de jour les réunions ne sont pas retournées.
     * @return array array<ReunionSignal>
     */
    public function findReunionsNotCancelled(int $days = 100): array
    {
        $dateLimit = new \DateTime(sprintf('-%d days', $days));

        return $this->createQueryBuilder('r')
            ->andWhere('r.DateReunion > :dateLimit')
            ->andWhere('r.ReunionAnnulee = :annulee')
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('annulee', 0)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return ReunionSignal[] Returns an array of ReunionSignal objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ReunionSignal
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
