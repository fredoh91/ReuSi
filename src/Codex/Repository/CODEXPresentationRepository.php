<?php

// namespace App\Codex\Repository;
// namespace App\Repository;
namespace App\Codex\Repository;

// use App\Codex\Entity\CODEXPresentation;
use App\Codex\Entity\CODEXPresentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CODEXPresentation>
 */
class CODEXPresentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CODEXPresentation::class);
    }



    public function auMoinsUnePresentationCommercialisee(string $codeVU): bool
    {
        $result = $this->createQueryBuilder('cp')
            ->select('cp.infoCommCourt')
            ->where('cp.codeVU = :codeVU')
            ->setParameter('codeVU', $codeVU)
            ->getQuery()
            ->getResult();

        foreach ($result as $row) {
            if ($row['infoCommCourt'] === 'Commercialisation') {
                return true;
            }
        }

        return false;
    }

    
    //    /**
    //     * @return CODEXPresentation[] Returns an array of CODEXPresentation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CODEXPresentation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
