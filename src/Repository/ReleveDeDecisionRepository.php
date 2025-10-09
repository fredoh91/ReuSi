<?php

namespace App\Repository;

use App\Entity\Signal;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ReleveDeDecision>
 */
class ReleveDeDecisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReleveDeDecision::class);
    }


    /**
     * Retourne le prochain numéro de RDD pour un signal donné
     *
     * @param integer $signalId
     * @return integer
     */
    public function donneNextNumeroRDD(int $signalId): int
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT MAX(r.NumeroRDD) 
            FROM App\Entity\ReleveDeDecision r
            WHERE r.SignalLie = :signalId'
        )->setParameter('signalId', $signalId);

        $maxNumero = $query->getSingleScalarResult();

        return $maxNumero !== null ? $maxNumero + 1 : 1;
    }


    /**
     * Trouve un RDD par signal et réunion, en excluant potentiellement un ID de RDD.
     *
     * @param Signal $signal
     * @param ReunionSignal $reunion
     * @param integer|null $excludedRddId L'ID du RDD à exclure de la recherche.
     * @return ReleveDeDecision|null
     */
    public function findOneBySignalAndReunionExcludingRdd(Signal $signal, ReunionSignal $reunion, ?int $excludedRddId): ?ReleveDeDecision
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.SignalLie = :signal')
            ->andWhere('r.reunionSignal = :reunion')
            ->setParameter('signal', $signal)
            ->setParameter('reunion', $reunion);

        if ($excludedRddId !== null) {
            $qb->andWhere('r.id != :excludedRddId')
               ->setParameter('excludedRddId', $excludedRddId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Trouve le dernier RDD pour un signal donné, basé sur la date de la réunion.
     *
     * @param Signal $signal
     * @return ReleveDeDecision|null
     */
    public function findLatestForSignal(Signal $signal): ?ReleveDeDecision
    {
        return $this->createQueryBuilder('rdd')
            ->leftJoin('rdd.reunionSignal', 'rs') // Utiliser leftJoin pour inclure les RDD sans réunion
            ->where('rdd.SignalLie = :signal')
            ->setParameter('signal', $signal)
            ->orderBy('rs.DateReunion', 'DESC')
            ->addOrderBy('rdd.id', 'DESC') // En cas de date identique, le plus récent ID prime
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    
    //    /**
    //     * @return ReleveDeDecision[] Returns an array of ReleveDeDecision objects
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

    //    public function findOneBySomeField($value): ?ReleveDeDecision
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
