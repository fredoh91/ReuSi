<?php

// namespace App\Codex\Repository;
// namespace App\Repository;
namespace App\Codex\Repository;

// use App\Codex\Entity\VUUtil;
// use App\Entity\VUUtil;
use App\Codex\Entity\SAVU;
use App\Codex\Entity\VUUtil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VUUtil>
 *
 * @method VUUtil|null find($id, $lockMode = null, $lockVersion = null)
 * @method VUUtil|null findOneBy(array $criteria, array $orderBy = null)
 * @method VUUtil[]    findAll()
 * @method VUUtil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VUUtilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VUUtil::class);
    }

    public function add(VUUtil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VUUtil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return VUUtil[] Returns an array of VUUtil objects
     */
    public function findByDeno($deno): array
    {
        return $this->createQueryBuilder('v')
            // ->andWhere($this->expr()->like('v.nomVU', ':deno'))
            // ->andWhere('v.nomVU = :deno')
            // ->setParameter('deno', '%' . $deno . '%')
            ->andWhere('v.nomVU LIKE :deno')
            ->setParameter('deno', '%' . $deno . '%')
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Permet de faire une recherche par Dénomination ou par Substance sur les champs libRech
     *
     * @param [type] $deno
     * @param [type] $sub
     * @return array
     */
    public function findByDenoOrBySub($sub, $deno): array
    {
        // Fonction de normalisation des paramètres
        $normalize = function ($str) {
            $str = mb_strtoupper($str ?? '', 'UTF-8');
            $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str); // retire les accents
            $str = preg_replace('/\s+/', '', $str); // retire les espaces
            return $str;
        };

        $denoNorm = $normalize($deno);
        $subNorm = $normalize($sub);
        /* Nom du titulaire */
        /* Nom du laboratoire */

        $qb = $this->createQueryBuilder('vu')
            ->select('vu.nomVU, 
                        vu.dbo_Autorisation_libAbr, 
                        vu.dbo_ClasseATC_libAbr, 
                        vu.dbo_ClasseATC_libCourt, 
                        vu.dbo_StatutSpeci_libAbr, 
                        sa.nomSubstance, 
                        vu.codeVU, 
                        vu.codeCIS, 
                        vu.codeDossier, 
                        vu.nomContactLibra,
                        vu.adresseContact, 
                        vu.adresseCompl, 
                        vu.codePost, 
                        vu.nomVille, 
                        vu.telContact, 
                        vu.faxContact, 
                        vu.nomActeurLong,
                        vu.adresse, 
                        vu.adresseComplExpl, 
                        vu.codePostExpl, 
                        vu.nomVilleExpl, 
                        vu.tel, 
                        vu.fax, 
                        vu.codeContact, 
                        vu.codeActeur, 
                        vu.libRechDenomination, 
                        sa.libRechSubstance')
            ->distinct()
            ->innerJoin(SAVU::class, 'sa', 'WITH', 'vu.codeVU = sa.codeVU')
            // ->where('vu.dbo_StatutSpeci_libAbr = :statut')
            // ->setParameter('statut', 'Actif')
            ->orderBy('vu.nomVU', 'ASC');

        if ($denoNorm) {
            $qb->andWhere('vu.libRechDenomination LIKE :deno')
                ->setParameter('deno', '%' . $denoNorm . '%');
        }

        if ($subNorm) {
            // Sous-requête pour le IN
            $subQb = $this->getEntityManager()->createQueryBuilder()
                ->select('DISTINCT sa2.codeVU')
                ->from(SAVU::class, 'sa2')
                ->where('sa2.libRechSubstance LIKE :sub');
            $qb->andWhere($qb->expr()->in('vu.codeVU', $subQb->getDQL()))
                ->setParameter('sub', '%' . $subNorm . '%');
        }


        // dump($qb->getQuery()->getDQL());
        // dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }

    

    /**
     * Permet de faire une recherche par Dénomination ou par Substance sur les champs libRech
     *
     * @param [type] $deno
     * @param [type] $sub
     * @return array
     */
    public function findByDenoOrBySubCourt($sub, $deno): array
    {
        // Fonction de normalisation des paramètres
        $normalize = function ($str) {
            $str = mb_strtoupper($str ?? '', 'UTF-8');
            $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str); // retire les accents
            $str = preg_replace('/\s+/', '', $str); // retire les espaces
            return $str;
        };

        $denoNorm = $normalize($deno);
        $subNorm = $normalize($sub);

        $qb = $this->createQueryBuilder('vu')
            ->select('vu.nomVU, 
                        vu.dbo_Autorisation_libAbr, 
                        vu.dbo_ClasseATC_libAbr, 
                        sa.nomSubstance, 
                        vu.codeVU, 
                        vu.codeCIS, 
                        vu.libRechDenomination, 
                        sa.libRechSubstance')
            ->distinct()
            ->innerJoin(SAVU::class, 'sa', 'WITH', 'vu.codeVU = sa.codeVU')
            // ->where('vu.dbo_StatutSpeci_libAbr = :statut')
            // ->setParameter('statut', 'Actif')
            ->orderBy('vu.nomVU', 'ASC');

        if ($denoNorm) {
            $qb->andWhere('vu.libRechDenomination LIKE :deno')
                ->setParameter('deno', '%' . $denoNorm . '%');
        }

        if ($subNorm) {
            // Sous-requête pour le IN
            $subQb = $this->getEntityManager()->createQueryBuilder()
                ->select('DISTINCT sa2.codeVU')
                ->from(SAVU::class, 'sa2')
                ->where('sa2.libRechSubstance LIKE :sub');
            $qb->andWhere($qb->expr()->in('vu.codeVU', $subQb->getDQL()))
                ->setParameter('sub', '%' . $subNorm . '%');
        }


        // dump($qb->getQuery()->getDQL());
        // dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return VUUtil[] Returns an array of VUUtil objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VUUtil
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
