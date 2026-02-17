<?php

namespace App\Repository;

use App\Entity\Suivi;
use App\Entity\ReunionSignal;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
            ->andWhere("r.statutReunion != 'annulee'")
            ->setParameter('dateLimit', $dateLimit)
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
    public function findReunionsNotCancelledAndNotLinkedToSignal(?int $signalId, int $days = 100, string $sensTri = 'ASC'): array
    {
        $dateLimit = new \DateTime(sprintf('-%d days', $days));

        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.DateReunion > :dateLimit')
            ->andWhere("r.statutReunion != 'annulee'")
            ->orderBy('r.DateReunion', $sensTri)
            ->setParameter('dateLimit', $dateLimit);

        if ($signalId !== null) {
            $subQuery = $this->getEntityManager()->createQueryBuilder()
                ->select('IDENTITY(su.reunionSignal)')
                ->from('App\Entity\Suivi', 'su')
                ->where('su.SignalLie = :signalId')
                ->andWhere('su.reunionSignal IS NOT NULL');

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->notIn('r.id', $subQuery->getDQL()),
                $qb->expr()->not(
                    $qb->expr()->exists(
                        $this->getEntityManager()->createQueryBuilder()
                            ->select('1')
                            ->from('App\Entity\Suivi', 'su2')
                            ->where('su2.SignalLie = :signalId')
                            ->getDQL()
                    )
                )
            ))
            ->setParameter('signalId', $signalId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Retourne la liste des "réunions signal" qui ne sont pas annulés et qui ne sont pas liées à un signal donné.
     *
     * @param integer $signalId
     * @param integer $days - Nombre de jours à considérer, au dela de ce nombre de jour les réunions ne sont pas retournées.
     * @return array array<ReunionSignal>
     */
    public function findReunionsNotCancelledAndNotLinkedToSignalAndUpper(int $signalId, int $days = 100, string $sensTri = 'ASC'): array
    {
        $dateLimit = new \DateTime(sprintf('-%d days', $days));
        // Récupérer la date maximale déjà attribuée au signal (via les Suivis liés)
        $maxDateQb = $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(rs.DateReunion)')
            ->from('App\Entity\Suivi', 'su')
            ->leftJoin('su.reunionSignal', 'rs')
            ->where('su.SignalLie = :signalId')
            ->andWhere('su.reunionSignal IS NOT NULL')
            ->setParameter('signalId', $signalId);

        $maxDateScalar = $maxDateQb->getQuery()->getSingleScalarResult();
        $maxDateAssigned = $maxDateScalar ? new \DateTime($maxDateScalar) : null;

        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.DateReunion > :dateLimit')
            ->andWhere("r.statutReunion != 'annulee'")
            ->orderBy('r.DateReunion', $sensTri)
            ->setParameter('dateLimit', $dateLimit);

        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(su.reunionSignal)')
            ->from('App\Entity\Suivi', 'su')
            ->where('su.SignalLie = :signalId')
            ->andWhere('su.reunionSignal IS NOT NULL');

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->notIn('r.id', $subQuery->getDQL()),
            $qb->expr()->not(
                $qb->expr()->exists(
                    $this->getEntityManager()->createQueryBuilder()
                        ->select('1')
                        ->from('App\Entity\Suivi', 'su2')
                        ->where('su2.SignalLie = :signalId')
                        ->getDQL()
                )
            )
        ))
        ->setParameter('signalId', $signalId);

        // Si une date maximale est déjà attribuée au signal, ne garder
        // que les réunions strictement supérieures à cette date.
        if ($maxDateAssigned) {
            $qb->andWhere('r.DateReunion > :maxDateAssigned')
               ->setParameter('maxDateAssigned', $maxDateAssigned);
        }


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
     * Retourne la liste des "réunions signal" qui ne sont pas annulés, qui ne sont pas liées à un signal donné et qui sont supérieures a la date du signal d'avant celui envoyé en paramétre.
     *
     * @param integer $signalId
     * @param integer $days - Nombre de jours à considérer, au dela de ce nombre de jour les réunions ne sont pas retournées.
     * @return array array<ReunionSignal>
     */
    public function findReunionsNotCancelledAndNotLinkedToSignalAndUpperWithSuivi(int $days = 100, string $sensTri = 'ASC', Suivi $suiviEnCours): array
    {

        $signalId = $suiviEnCours->getSignalLie();

        $dateLimit = new \DateTime(sprintf('-%d days', $days));

        $suiviPrecedent = $this->getEntityManager()->getRepository(Suivi::class)->findSuiviByIdSignalAndNumeroSuivi($signalId, $suiviEnCours->getNumeroSuivi() - 1);

        $dateSuiviPrecedent = $suiviPrecedent && $suiviPrecedent->getReunionSignal() ? $suiviPrecedent->getReunionSignal()->getDateReunion() : null;

        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.DateReunion > :dateLimit')
            ->andWhere("r.statutReunion != 'annulee'")
            ->andWhere('r.DateReunion >= :dateSuiviPrecedent')
            ->orderBy('r.DateReunion', $sensTri)
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('dateSuiviPrecedent', $dateSuiviPrecedent);


        if ($signalId !== null) {
            $subQuery = $this->getEntityManager()->createQueryBuilder()
                ->select('IDENTITY(su.reunionSignal)')
                ->from('App\Entity\Suivi', 'su')
                ->where('su.SignalLie = :signalId')
                ->andWhere('su.reunionSignal IS NOT NULL');

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->notIn('r.id', $subQuery->getDQL()),
                $qb->expr()->not(
                    $qb->expr()->exists(
                        $this->getEntityManager()->createQueryBuilder()
                            ->select('1')
                            ->from('App\Entity\Suivi', 'su2')
                            ->where('su2.SignalLie = :signalId')
                            ->getDQL()
                    )
                )
            ))
            ->setParameter('signalId', $signalId);
        }

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
