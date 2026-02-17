<?php

namespace App\Repository;

use App\Entity\LiensReunionsSignal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiensReunionsSignal>
 * @method LiensReunionsSignal|null find($id, $lockMode = null, $lockVersion = null)
 * @method LiensReunionsSignal|null findOneBy(array $criteria, array $orderBy = null)
 * @method LiensReunionsSignal[]    findAll()
 * @method LiensReunionsSignal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiensReunionsSignalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiensReunionsSignal::class);
    }

    //    /**
    //     * @return LiensReunionsSignal[] Returns an array of LiensReunionsSignal objects
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

    //    public function findOneBySomeField($value): ?LiensReunionsSignal
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
