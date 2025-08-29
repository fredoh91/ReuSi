<?php

// namespace App\Codex\Repository;
// namespace App\Repository;
namespace App\Codex\Repository;

// use App\Codex\Entity\SAVU;
// use App\Entity\SAVU;
use App\Codex\Entity\SAVU;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SAVU>
 *
 * @method SAVU|null find($id, $lockMode = null, $lockVersion = null)
 * @method SAVU|null findOneBy(array $criteria, array $orderBy = null)
 * @method SAVU[]    findAll()
 * @method SAVU[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SAVURepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SAVU::class);
    }

    public function add(SAVU $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SAVU $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de retourner un Array de VUUtils à partir de son CodeCIS
     * @return SAVU[] Returns an array of VUUtil objects
     */
    public function findByCodeCIS($codeCIS): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.codeCIS = :codeCIS')
            ->setParameter('codeCIS', $codeCIS)
            ->orderBy('s.numComposant', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Permet de retourner une string qui correspond a la liste des DCI séparés par un slash
     * @return string : DCI
     */
    public function findByCodeCIS_DCI($codeCIS): string
    {
        $saVu = $this->createQueryBuilder('s')
            ->andWhere('s.codeCIS = :codeCIS')
            ->setParameter('codeCIS', $codeCIS)
            ->orderBy('s.numComposant', 'ASC')
            ->getQuery()
            ->getResult();

        if ($saVu) {
            $dciList = '';
            foreach ($saVu as $composant) {
                $dciList .= $dciList ? ' / ' . $composant->getNomSubstance() : $composant->getNomSubstance();
            }
            return $dciList;
        } else {
            return '';
        }
    }


    /**
     * Permet de retourner un tableau de string qui correspond a :
     *    - la liste des DCI séparés par un slash
     *    - la liste des dosages séparés par un slash
     *
     * @param [type] $codeCIS
     * @return array<string> 
     */
    public function findByCodeCIS_DCI_Dosage($codeCIS): array
    {
        $saVu = $this->createQueryBuilder('s')
            ->andWhere('s.codeCIS = :codeCIS')
            ->setParameter('codeCIS', $codeCIS)
            ->orderBy('s.numComposant', 'ASC')
            ->getQuery()
            ->getResult();

        if ($saVu) {
            $dciList = '';
            $dosageList = '';
            foreach ($saVu as $composant) {
                $dciList .= $dciList ? ' / ' . $composant->getNomSubstance() : $composant->getNomSubstance();
                $dosageList .= $dosageList ? ' / ' . $composant->getDosageLibra() : $composant->getDosageLibra();
            }
            return [$dciList, $dosageList];
        } else {
            return ['', ''];
        }
    }


    /**
     * Undocumented function
     *
     * @param [type] $codeCIS
     * @return string
     */
    public function findByCodeCIS_VoieAdmin($codeCIS): string
    {
        $saVu = $this->createQueryBuilder('s')
            ->select('DISTINCT s.libCourt')
            ->andWhere('s.codeCIS = :codeCIS')
            ->setParameter('codeCIS', $codeCIS)
            ->orderBy('s.numComposant', 'ASC')
            ->getQuery()
            ->getResult();

        if ($saVu) {
            $voieAdmin = $saVu[0]['libCourt'];
            return $voieAdmin;
        } else {
            return '';
        }
    }



    //    /**
    //     * @return SAVU[] Returns an array of SAVU objects
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

    //    public function findOneBySomeField($value): ?SAVU
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
