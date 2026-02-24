<?php

// namespace App\Repository;
namespace App\Codex\Repository;

// use App\Entity\SubSIMAD;
use App\Codex\Entity\SubSIMAD;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubSIMAD>
 */
class SubSIMADRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubSIMAD::class);
    }

    /**
     * Permet de faire une recherche par Dénomination ou par DCI pour les substances non-médicamenteuses
     *
     * @param string|null $dci
     * @param string|null $denomination
     * @return array
     */
    public function findByDenoOrBySub(?string $dci, ?string $denomination): array
    {
        // Fonction de normalisation des paramètres
        $normalize = function (?string $str) {
            $str = mb_strtoupper($str ?? '', 'UTF-8');
            $transliterator = \Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', \Transliterator::FORWARD);
            $str = $transliterator->transliterate($str);
            $str = preg_replace('/\s+/', '', $str); // retire les espaces
            return $str;
        };

        $denominationNorm = $normalize($denomination);
        $dciNorm = $normalize($dci);

        $qb = $this->createQueryBuilder('s');

        // Sélection des champs comme dans la requête Access sSQL_Simad
        $qb->select("s.id AS id, s.productname AS nomVU, 
                        '' AS dbo_Autorisation_libAbr, 
                        '' AS dbo_ClasseATC_libAbr, 
                        '' AS dbo_ClasseATC_libCourt, 
                        '' AS dbo_StatutSpeci_libAbr, 
                        s_pt.productname AS nomSubstance, 
                        '' AS DP, 
                        s.cas_id AS codeVU, 
                        '' AS codeCIS, 
                        '' AS codeDossier, 
                        '' AS nomContactLibra, 
                        '' AS adresseContact, 
                        '' AS adresseCompl, 
                        '' AS codePost, 
                        '' AS nomVille, 
                        '' AS telContact, 
                        '' AS faxContact, 
                        '' AS nomActeurLong, 
                        '' AS adresse, 
                        '' AS adresseComplExpl, 
                        '' AS codePostExpl, 
                        '' AS nomVilleExpl, 
                        '' AS tel, 
                        '' AS fax, 
                        '' AS codeContact, 
                        '' AS codeActeur, 
                        'NonMedic' AS typeSubstance, 
                        s.productfamily, 
                        s.unii_id, 
                        s.cas_id,
                        s.productname AS libRechDenomination,
                        s_pt.productname AS libRechSubstance
                        ")
            ->leftJoin(SubSIMAD::class, 's_pt', 'WITH', 's.unii_id = s_pt.unii_id AND s_pt.topproductname = \'PT\'');
        
        $hasWhere = false;
        if ($denominationNorm) {
            $qb->andWhere('s.productname LIKE :denomination')
                ->setParameter('denomination', '%' . $denominationNorm . '%');
            $hasWhere = true;
        }

        if ($dciNorm) {
            // Dans la requête Access, le dci correspond à RqSubSIMAD_PT.productname
            $qb->andWhere('s_pt.productname LIKE :dci')
                ->setParameter('dci', '%' . $dciNorm . '%');
            $hasWhere = true;
        }
        
        if (!$hasWhere) {
             // Si aucun critère de recherche n'est fourni, ne rien retourner
            return [];
        }

        $qb->orderBy('s.productname', 'ASC');

        // dump($qb->getQuery()->getDQL());
        // dump($qb->getQuery()->getSQL());

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return [];
        }

        // Récupération des unii_id pour charger les produits liés
        $uniiIds = array_filter(array_unique(array_column($results, 'unii_id')));
        $grouped = [];

        if (!empty($uniiIds)) {
            $related = $this->createQueryBuilder('s2')
                ->select('s2.unii_id, s2.productname, s2.topproductname')
                ->where('s2.unii_id IN (:ids)')
                ->setParameter('ids', $uniiIds)
                ->getQuery()
                ->getResult();

            foreach ($related as $rel) {
                $grouped[$rel['unii_id']][] = [
                    'productName' => $rel['productname'],
                    'topProductName' => $rel['topproductname']
                ];
            }
        }

        foreach ($results as &$row) {
            $row['allProductName'] = $grouped[$row['unii_id']] ?? [];
        }

        return $results;
    }

    //    /**
    //     * @return SubSIMAD[] Returns an array of SubSIMAD objects
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

    //    public function findOneBySomeField($value): ?SubSIMAD
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
