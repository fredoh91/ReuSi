<?php

namespace App\Repository;

use App\Entity\Signal;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Signal>
 */
class SignalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Signal::class);
    }

    /**
     * @return Signal[] Returns an array of Signal objects
     */
    public function findByTypeSignal($TypeSignal): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.TypeSignal = :TypeSignal')
            ->setParameter('TypeSignal', $TypeSignal)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Construit une requête pour trouver des signaux basés sur plusieurs critères.
     * @return QueryBuilder
     */
    public function findByTypeSignalWithCriteria(string $typeSignal, array $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.TypeSignal = :typeSignal')
            ->setParameter('typeSignal', $typeSignal);

        // Recherche sur le Titre
        if (!empty($criteria['Titre'])) {
            $qb->andWhere('s.Titre LIKE :titre')
               ->setParameter('titre', '%' . $criteria['Titre'] . '%');
        }

        // Recherche sur l'Indication
        if (!empty($criteria['Indication'])) {
            $qb->andWhere('s.Indication LIKE :indication')
               ->setParameter('indication', '%' . $criteria['Indication'] . '%');
        }
        
        // Recherche sur le Contexte
        if (!empty($criteria['Contexte'])) {
            $qb->andWhere('s.Contexte LIKE :contexte')
               ->setParameter('contexte', '%' . $criteria['Contexte'] . '%');
        }

        // Recherche sur la DCI ou la Dénomination du produit
        if (!empty($criteria['dci']) || !empty($criteria['denomination'])) {
            $qb->leftJoin('s.produits', 'p');
            if (!empty($criteria['dci'])) {
                $qb->andWhere('p.DCI LIKE :dci')
                   ->setParameter('dci', '%' . $criteria['dci'] . '%');
            }
            if (!empty($criteria['denomination'])) {
                $qb->andWhere('p.Denomination LIKE :denomination')
                   ->setParameter('denomination', '%' . $criteria['denomination'] . '%');
            }
        }

        // Recherche sur la description (Signal, Suivi, RDD)
        if (!empty($criteria['Description'])) {
            $qb->leftJoin('s.suivis', 'suivi')
               ->leftJoin('s.releveDeDecisions', 'rdd')
               ->andWhere($qb->expr()->orX(
                   's.DescriptionSignal LIKE :description',
                   'suivi.DescriptionSuivi LIKE :description',
                   'rdd.DescriptionRDD LIKE :description'
               ))
               ->setParameter('description', '%' . $criteria['Description'] . '%');
        }
        
        // Recherche sur les dates de réunion
        if (!empty($criteria['dateReunionDebut']) || !empty($criteria['dateReunionFin'])) {
            $qb->leftJoin('s.reunionSignals', 'reunion');
            
            if (!empty($criteria['dateReunionDebut'])) {
                $qb->andWhere('reunion.DateReunion >= :dateReunionDebut')
                   ->setParameter('dateReunionDebut', $criteria['dateReunionDebut']);
            }
            if (!empty($criteria['dateReunionFin'])) {
                $qb->andWhere('reunion.DateReunion <= :dateReunionFin')
                   ->setParameter('dateReunionFin', $criteria['dateReunionFin']);
            }
        }

        $qb->orderBy('s.id', 'DESC')
           ->distinct(); // Pour éviter les doublons à cause des jointures

        return $qb;
    }

    public function findForSuiviAddition(string $typeSignal, array $criteria, \App\Entity\ReunionSignal $reunion): QueryBuilder
    {
        $qb = $this->findByTypeSignalWithCriteria($typeSignal, $criteria);

        // Condition 1: Exclure les signaux ayant déjà un Suivi pour la réunion EN COURS
        $qb->leftJoin('s.suivis', 'existing_suivi_for_current_reunion', \Doctrine\ORM\Query\Expr\Join::WITH, 'existing_suivi_for_current_reunion.reunionSignal = :reunionId')
           ->andWhere('existing_suivi_for_current_reunion.id IS NULL');

        // Condition 2: Exclure les signaux ayant un Suivi pour une réunion FUTURE
        // Sous-requête pour trouver les IDs des signaux qui ont un suivi lié à une réunion future
        $subQuerySignalsWithFutureReunion = $this->getEntityManager()->createQueryBuilder()
            ->select('s_future.id')
            ->from('App\Entity\Signal', 's_future')
            ->join('s_future.suivis', 'su_future')
            ->join('su_future.reunionSignal', 'rs_future')
            ->where('rs_future.DateReunion > :reunionDate');

        $qb->andWhere($qb->expr()->notIn('s.id', $subQuerySignalsWithFutureReunion->getDQL()));

        // Condition 3: Antériorité
        // Exclure les signaux dont la dernière date de réunion associée est >= à la réunion en cours
        // OU les signaux sans réunion associée dont la date de création est >= à la réunion en cours

        // Sous-requête pour trouver les IDs des signaux dont la dernière date de réunion associée est >= à la réunion en cours
        $subQuerySignalsWithRecentReunion = $this->getEntityManager()->createQueryBuilder()
            ->select('s_recent.id')
            ->from('App\Entity\Signal', 's_recent')
            ->leftJoin('s_recent.suivis', 'su_recent')
            ->leftJoin('su_recent.reunionSignal', 'rs_recent')
            ->groupBy('s_recent.id')
            ->having('MAX(rs_recent.DateReunion) >= :reunionDate');

        // Sous-requête pour trouver les IDs des signaux sans réunion associée dont la date de création est >= à la réunion en cours
        $subQuerySignalsCreatedRecentlyWithoutReunion = $this->getEntityManager()->createQueryBuilder()
            ->select('s_created_recent.id')
            ->from('App\Entity\Signal', 's_created_recent')
            ->leftJoin('s_created_recent.suivis', 'su_created_recent')
            ->leftJoin('su_created_recent.reunionSignal', 'rs_created_recent')
            ->where('s_created_recent.CreatedAt >= :reunionDate')
            ->groupBy('s_created_recent.id')
            ->having('COUNT(rs_created_recent.id) = 0');

        $qb->andWhere($qb->expr()->notIn('s.id', $subQuerySignalsWithRecentReunion->getDQL()))
           ->andWhere($qb->expr()->notIn('s.id', $subQuerySignalsCreatedRecentlyWithoutReunion->getDQL()));

        $qb->setParameter('reunionDate', $reunion->getDateReunion());
        $qb->setParameter('reunionId', $reunion->getId());

        return $qb;
    }
}