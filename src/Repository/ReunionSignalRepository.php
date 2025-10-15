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


    /**
     * Retourne la liste des "réunions signal" qui ne sont pas annulés et qui ne sont pas liées à un signal donné.
     *
     * @param integer $signalId
     * @param integer $days - Nombre de jours à considérer, au dela de ce nombre de jour les réunions ne sont pas retournées.
     * @return array array<ReunionSignal>
     */
    public function findReunionsNotCancelledAndNotLinkedToSignal(int $signalId, int $days = 100, string $sensTri = 'ASC'): array
    {
        $dateLimit = new \DateTime(sprintf('-%d days', $days));

        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.DateReunion > :dateLimit')
            ->andWhere('r.ReunionAnnulee = :annulee')
            ->orderBy('r.DateReunion', $sensTri)
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('annulee', 0);

        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(rdd.reunionSignal)')
            ->from('App\Entity\ReleveDeDecision', 'rdd')
            ->where('rdd.SignalLie = :signalId')
            ->andWhere('rdd.reunionSignal IS NOT NULL');

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->notIn('r.id', $subQuery->getDQL()),
            $qb->expr()->not(
                $qb->expr()->exists(
                    $this->getEntityManager()->createQueryBuilder()
                        ->select('1')
                        ->from('App\Entity\ReleveDeDecision', 'rdd2')
                        ->where('rdd2.SignalLie = :signalId')
                        ->getDQL()
                )
            )
        ))
        ->setParameter('signalId', $signalId);


        // Exclure les réunions déjà liées à un RDD du signal
        // $qb->andWhere('r.id NOT IN (
        //     SELECT IDENTITY(rdd.reunionSignal)
        //     FROM App\Entity\ReleveDeDecision rdd
        //     WHERE rdd.SignalLie = :signalId
        // )')
        // ->setParameter('signalId', $signalId);

// dump($qb->getQuery()->getSQL());


        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère toutes les réunions avec les RDDs, Signaux et Produits associés pour optimiser l'affichage.
     * @return ReunionSignal[]
     */
    public function findAllWithDetails(): array
    {
        return $this->createQueryBuilder('rs')
            ->select('DISTINCT rs', 'rdd', 's', 'p')
            ->leftJoin('rs.ReleveDeDecision', 'rdd')
            ->leftJoin('rdd.SignalLie', 's')
            ->leftJoin('s.produits', 'p')
            ->orderBy('rs.DateReunion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les ReunionSignal en fonction de plusieurs critères de recherche.
     *
     * @param array|null $criteria
     * @return ReunionSignal[]
     */
    public function findByCriteriaWithDetails(?array $criteria): array
    {
        $qb = $this->createQueryBuilder('rs');

        $qb->select('DISTINCT rs', 'rdd', 's', 'p')
            // Jointure avec les RDDs de la réunion
            ->leftJoin('rs.ReleveDeDecision', 'rdd')
            // Jointure avec les signaux liés aux RDDs
            ->leftJoin('rdd.SignalLie', 's')
            // Jointure avec les produits liés aux signaux
            ->leftJoin('s.produits', 'p')
            // Charger les suivis liés à cette réunion
            ->leftJoin('s.suivis', 'suivis', 'WITH', 'suivis.reunionSignal = rs.id')
            ->addSelect('suivis')
            ->leftJoin('suivis.RddLie', 'suivi_rdd')
            ->addSelect('suivi_rdd');

        if (!empty($criteria['dateDebut'])) {
            $qb->andWhere('rs.DateReunion >= :dateDebut')
               ->setParameter('dateDebut', $criteria['dateDebut']);
        }

        if (!empty($criteria['dateFin'])) {
            $qb->andWhere('rs.DateReunion <= :dateFin')
               ->setParameter('dateFin', $criteria['dateFin']);
        }

        if (!empty($criteria['recherche'])) {
            $searchTerm = '%' . $criteria['recherche'] . '%';
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('s.Titre', ':searchTerm'),
                $qb->expr()->like('s.DescriptionSignal', ':searchTerm'),
                $qb->expr()->like('rdd.DescriptionRDD', ':searchTerm'),
                $qb->expr()->like('p.Denomination', ':searchTerm'),
                $qb->expr()->like('p.DCI', ':searchTerm')
            ))->setParameter('searchTerm', $searchTerm);
        }

        $qb->orderBy('rs.DateReunion', 'DESC');

        return $qb->getQuery()->getResult();
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
