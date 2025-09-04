<?php

namespace App\Repository;

use App\Entity\ReleveDeDecision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
