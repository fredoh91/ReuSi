<?php

namespace App\Controller;

use App\Entity\Signal;
use App\Entity\Produits;
use App\Codex\Entity\SAVU;
use App\Codex\Entity\VUUtil;
use App\Form\CodexSearchType;
use App\Form\ProduitsType;
use App\Repository\SignalRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Codex\Repository\CODEXPresentationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class GestionProduitsController extends AbstractController
{

    private $codexPresentationRepository;

    public function __construct(CODEXPresentationRepository $codexPresentationRepository)
    {
        $this->codexPresentationRepository = $codexPresentationRepository;
    }

    #[Route('/signal/{signalId}/creation_produits', name: 'app_creation_produits')]
    public function creation_produits(int $signalId, SignalRepository $signalRepo, Request $request, ManagerRegistry $doctrine): Response
    {
        $signal = $signalRepo->find($signalId);

        // Gérer le cas où le signal n'existe pas
        if (!$signal ) {
            throw $this->createNotFoundException('Le signal avec l\'id ' . $signalId . ' n\'existe pas.');
        }

        $form = $this->createForm(CodexSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($form->get('recherche')->isClicked()) {
                if ($form->isValid()) {

                    $data = $form->getData();
                    // dd($data);
                    [$medics, $NbMedics] = $this->getMedics($doctrine, $data);
                    // dd([$medics, $NbMedics]);
                }
            }
            if ($form->get('reset')->isClicked()) {
                return $this->redirectToRoute('app_creation_produits', ['signalId' => $signalId]);
            }
            if ($form->get('annulation')->isClicked()) {
                return $this->redirectToRoute('app_signal_new', ['signalId' => $signalId]);
            }
        }



        return $this->render('gestion_produits/creation_produit_recherche.html.twig', [
            'form' => $form->createView(),
            'medics' => $medics ?? [],
            'NbMedics' => $NbMedics ?? 0,
            'signalId' => $signalId,
        ]);
    }


    #[Route('/signal/{signalId}/ajout_produit/{codeCIS}', name: 'app_ajout_produit', defaults: ['codeCIS' => null])]
    public function ajout_produits(int $signalId, ?string $codeCIS = null, SignalRepository $signalRepo, Request $request, ManagerRegistry $doctrine): Response
    {
        $signal = $signalRepo->find($signalId);

        // Gérer le cas où le signal n'existe pas
        if (!$signal) {
            throw $this->createNotFoundException('Ce signal n\'existe pas');
        }

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        if ($codeCIS) {
            // Traitement si codeCIS est fourni
            $vuUtil = $doctrine
                ->getRepository(VUUtil::class, 'codex')
                ->findByCodeCIS($codeCIS);
            [$DCI, $dosage] = $doctrine
                ->getRepository(SAVU::class, 'codex')
                ->findByCodeCIS_DCI_Dosage($codeCIS);
            $voieAdmin = $doctrine
                ->getRepository(SAVU::class, 'codex')
                ->findByCodeCIS_VoieAdmin($codeCIS);

            // dump($vuUtil[0]);
            // dump($DCI);
            $produit = new Produits();
            $produit->setSignalLie($signal);
            $produit->setDenomination($vuUtil[0]->getNomVU());
            $produit->setDosage($dosage);
            $produit->setDci($DCI);
            $produit->setVoie($voieAdmin);
            $produit->setCodeATC($vuUtil[0]->getDboClasseATCLibAbr());
            $produit->setLibATC($vuUtil[0]->getDboClasseATCLibCourt());
            $produit->setTypeProcedure($vuUtil[0]->getDboAutorisationLibAbr());
            $produit->setCodeCIS($vuUtil[0]->getCodeCIS());
            $produit->setCodeVU($vuUtil[0]->getCodeVU());
            $produit->setCodeDossier(trim($vuUtil[0]->getCodeDossier()));
            $produit->setNomVU($vuUtil[0]->getNomVU());
            // Titulaire
            $produit->setIdTitulaire(trim($vuUtil[0]->getCodeContact()));
            $produit->setTitulaire($vuUtil[0]->getNomContactLibra());
            $produit->setAdresseContact($vuUtil[0]->getAdresseContact());
            $produit->setAdresseCompl($vuUtil[0]->getAdresseCompl());
            $produit->setCodePost($vuUtil[0]->getCodePost());
            $produit->setNomVille($vuUtil[0]->getNomVille());
            $produit->setTelContact($vuUtil[0]->getTelContact());
            $produit->setFaxContact($vuUtil[0]->getFaxContact());
            $produit->setDboPaysLibAbr($vuUtil[0]->getDboPaysLibAbr());

            // Laboratoire
            $produit->setIdLaboratoire(trim($vuUtil[0]->getCodeActeur()));
            $produit->setLaboratoire($vuUtil[0]->getNomActeurLong());
            $produit->setAdresse($vuUtil[0]->getAdresse());
            $produit->setAdresseComplExpl($vuUtil[0]->getAdresseComplExpl());
            $produit->setCodePostExpl($vuUtil[0]->getCodePostExpl());
            $produit->setNomVilleExpl($vuUtil[0]->getNomVilleExpl());

            $produit->setStatutActifSpecialite($vuUtil[0]->getDboStatutSpeciLibAbr());
            // dd($produit);
        } else {
            // Traitement si codeCIS n'est pas fourni
            $produit = new Produits();
            $produit->setSignalLie($signal);
        }


        $produit->setCreatedAt(new \DateTimeImmutable());
        $produit->setUpdatedAt(new \DateTimeImmutable());
        $produit->setUserCreate($userName);
        $produit->setUserModif($userName);


        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($form->get('validation')->isClicked()) {
                if ($form->isValid()) {

                    $data = $form->getData();
                    // dd($data);
                    // [$medics, $NbMedics] = $this->getMedics($doctrine, $data);
                    // dd([$medics, $NbMedics]);
                    $em = $doctrine->getManager();
                    $em->persist($produit);
                    $em->flush();
                    // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signalId]);
                    return $this->redirectToRoute('app_signal_modif', ['signalId' => $signalId]);
                }
            }
            // if ($form->get('reset')->isClicked()) {
            //     return $this->redirectToRoute('app_creation_produits', ['signalId' => $signalId]);
            // }
            if ($form->get('annulation')->isClicked()) {
                // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signalId]);
                    return $this->redirectToRoute('app_signal_modif', ['signalId' => $signalId]);
            }
        }



        return $this->render('gestion_produits/ajout_produit_recherche.html.twig', [
            'form' => $form->createView(),
            'medics' => $medics ?? [],
            'NbMedics' => $NbMedics ?? 0,
            'signalId' => $signalId,
        ]);
    }


    /**
     * Regroupe les médicaments par codeCIS à partir des données du formulaire
     */
    private function getMedics(ManagerRegistry $doctrine, array $data): array
    {
        $em = $doctrine->getManagerForClass(VUUtil::class);
        // dd($em->getConnection()->getParams());

        $results = $doctrine
            ->getRepository(VUUtil::class, 'codex')
            ->findByDenoOrBySub($data['dci'], $data['denomination']);
        $medics = [];
        foreach ($results as $row) {
            $cis = $row['codeCIS'];
            if (!isset($medics[$cis])) {
                $medics[$cis] = [
                    'nomVU' => $row['nomVU'],
                    'dbo_Autorisation_libAbr' => $row['dbo_Autorisation_libAbr'],
                    'dbo_ClasseATC_libAbr' => $row['dbo_ClasseATC_libAbr'],
                    'dbo_ClasseATC_libCourt' => $row['dbo_ClasseATC_libCourt'],
                    'dbo_StatutSpeci_libAbr' => $row['dbo_StatutSpeci_libAbr'],
                    'codeVU' => $row['codeVU'],
                    'codeCIS' => $row['codeCIS'],
                    'codeDossier' => $row['codeDossier'],
                    'nomContactLibra' => $row['nomContactLibra'],
                    'adresseContact' => $row['adresseContact'],
                    'adresseCompl' => $row['adresseCompl'],
                    'codePost' => $row['codePost'],
                    'nomVille' => $row['nomVille'],
                    'telContact' => $row['telContact'],
                    'faxContact' => $row['faxContact'],
                    'nomActeurLong' => $row['nomActeurLong'],
                    'adresse' => $row['adresse'],
                    'adresseComplExpl' => $row['adresseComplExpl'],
                    'codePostExpl' => $row['codePostExpl'],
                    'nomVilleExpl' => $row['nomVilleExpl'],
                    'tel' => $row['tel'],
                    'fax' => $row['fax'],
                    'codeContact' => $row['codeContact'],
                    'codeActeur' => $row['codeActeur'],
                    'libRechDenomination' => $row['libRechDenomination'],
                    'substances' => [],
                    'commercialise' => $this->codexPresentationRepository->auMoinsUnePresentationCommercialisee($row['codeVU']),
                ];
            }
            $medics[$cis]['substances'][] = $row['nomSubstance'];
        }
        $NbMedics = count($medics);
        return [$medics, $NbMedics];
    }
}
