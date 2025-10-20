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
}
