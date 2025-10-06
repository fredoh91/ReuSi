<?php

namespace App\Repository;

use App\Entity\Suivi;
use App\Entity\Signal;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Suivi>
 */
class SuiviRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suivi::class);
    }




    /**
     * Retourne le prochain numéro de suivi pour un signal donné
     *
     * @param integer $signalId
     * @return integer
     */
    public function donneNextNumeroSuivi(int $signalId): int
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT MAX(s.NumeroSuivi) 
            FROM App\Entity\Suivi s
            WHERE s.SignalLie = :signalId'
        )->setParameter('signalId', $signalId);

        $maxNumero = $query->getSingleScalarResult();

        return $maxNumero !== null ? $maxNumero + 1 : 1;
    }


    /**
     * Trouve le dernier RDD pour un signal donné, basé sur la date de la réunion.
     *
     * @param Signal $signal
     * @return Suivi|null
     */
    public function findLatestForSignal(Signal $signal): ?Suivi
    {
        return $this->createQueryBuilder('s')
            ->join('s.reunionSignal', 'rs')
            ->where('s.SignalLie = :signal')
            ->setParameter('signal', $signal)
            ->orderBy('rs.DateReunion', 'DESC')
            ->addOrderBy('s.id', 'DESC') // En cas de date identique, le plus récent ID prime
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve tous les suivis pour un signal donné, en excluant le suivi initial (NumeroSuivi = 0).
     *
     * @param Signal $signal
     * @return Suivi[]
     */
    public function findForSignalExcludingInitial(Signal $signal): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.SignalLie = :signal')
            ->andWhere('s.NumeroSuivi != :numeroSuiviInitial')
            ->setParameter('signal', $signal)
            ->setParameter('numeroSuiviInitial', 0)
            ->orderBy('s.NumeroSuivi', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve le suivi initial (NumeroSuivi = 0) pour un signal donné.
     *
     * @param Signal $signal
     * @return Suivi|null
     */
    public function findInitialForSignal(Signal $signal): ?Suivi
    {
        return $this->createQueryBuilder('s')
            ->where('s.SignalLie = :signal')
            ->andWhere('s.NumeroSuivi = :numeroSuiviInitial')
            ->setParameter('signal', $signal)
            ->setParameter('numeroSuiviInitial', 0)
            ->getQuery()
            ->getOneOrNullResult();
    }



    //    /**
    //     * @return Suivi[] Returns an array of Suivi objects
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

    //    public function findOneBySomeField($value): ?Suivi
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
