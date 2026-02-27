<?php

namespace App\Controller;

use App\Codex\Entity\SAVU;
use App\Codex\Entity\SubSIMAD; // Ajout
use App\Codex\Entity\VUUtil;
use App\Codex\Repository\CODEXPresentationRepository;
// use App\Codex\Repository\SubSIMADRepository; // Ajout
use App\Entity\Produits;
use App\Entity\Signal;
use App\Form\CodexSearchType;
use App\Form\ProduitsMedType;
use App\Form\ProduitsNonMedType;
use App\Repository\SignalRepository;
// use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

final class GestionProduitsController extends AbstractController
{

    private $codexPresentationRepository;
    private $logger;
    private $kernel;

    public function __construct(CODEXPresentationRepository $codexPresentationRepository,LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->codexPresentationRepository = $codexPresentationRepository;
        $this->logger = $logger;
        $this->kernel = $kernel;
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

        $SubMed = [];
        $SubNonMed = [];
        $NbMedics = null;

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($form->get('recherche')->isClicked()) {
                if ($form->isValid()) {

                    $data = $form->getData();
                    [$medics, $NbMedics] = $this->getMedics($doctrine, $data);

                    $SubMed = $medics['SubMed'];
                    $SubNonMed = $medics['SubNonMed'];
                }
            }
            if ($form->get('reset')->isClicked()) {
                return $this->redirectToRoute('app_creation_produits', ['signalId' => $signalId]);
            }
            if ($form->get('annulation')->isClicked()) {
                return $this->redirectAfterProductModification($request, $signalId);
            }
        }

        return $this->render('gestion_produits/creation_produit_recherche.html.twig', [
            'form' => $form->createView(),
            'SubMed' => $SubMed,
            'SubNonMed' => $SubNonMed,
            'NbMedics' => $NbMedics,
            'signalId' => $signalId,
        ]);
    }


    #[Route('/signal/{signalId}/modification_produits/{produitId}', name: 'app_modif_produits')]
    public function modif_produits(int $signalId, int $produitId, SignalRepository $signalRepo, Request $request, ManagerRegistry $doctrine): Response
    {
        $signal = $signalRepo->find($signalId);

        // Gérer le cas où le signal n'existe pas
        if (!$signal ) {
            throw $this->createNotFoundException('Le signal avec l\'id ' . $signalId . ' n\'existe pas.');
        }

        $produit = $doctrine->getRepository(Produits::class)->find($produitId);
        if (!$produit) {
            throw $this->createNotFoundException('Le produit avec l\'id ' . $produitId . ' n\'existe pas.');
        }

        $typeSubstance = $produit->getTypeSubstance();

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        if ($typeSubstance === 'Medic') {
            $form = $this->createForm(ProduitsMedType::class, $produit,[
                'show_delete_button' => true, // Affiche le bouton de suppression pour les produits médicinaux
            ]);
            $vue = 'gestion_produits/ajout_produit_med_recherche.html.twig';
        } else if ($typeSubstance === 'NonMedic') {
            $form = $this->createForm(ProduitsNonMedType::class, $produit,[
                'show_delete_button' => true, // Affiche le bouton de suppression pour les produits non-médicinaux
            ]);
            $vue = 'gestion_produits/ajout_produit_non_med_recherche.html.twig';
        } else if ($typeSubstance === 'SaisieManu') {
            $form = $this->createForm(ProduitsMedType::class, $produit,[
                'show_delete_button' => true, // Affiche le bouton de suppression pour les produits de saisie manuelle
            ]);
        } else {
            throw $this->createNotFoundException('Type de substance inconnu pour le produit avec l\'id ' . $produitId);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($form->get('validation')->isClicked()) {
                if ($form->isValid()) {
                    $produit->setUpdatedAt(new \DateTimeImmutable());
                    $produit->setUserModif($userName);

                    $em = $doctrine->getManager();
                    $em->persist($produit);
                    $em->flush();

                    $this->addFlash('success', 'Le produit ' . $produit->getId() . ' a été modifié avec succès.');

                    return $this->redirectAfterProductModification($request, $signalId);
                }
            }

            if ($form->get('delete')->isClicked()) {
                $em = $doctrine->getManager();
                $em->remove($produit);
                $em->flush();
                $this->addFlash('success', 'Le produit ' . $produit->getId() . ' a été supprimé avec succès.');
                return $this->redirectAfterProductModification($request, $signalId);
            }

            if ($form->get('annulation')->isClicked()) {
                return $this->redirectAfterProductModification($request, $signalId);
            }
        }

        return $this->render($vue, [
            'form' => $form->createView(),
            'signalId' => $signalId,
        ]);
    }


    #[Route('/signal/{signalId}/ajout_produit_med/{codeCIS}', name: 'app_ajout_produit_med', defaults: ['codeCIS' => null])]
    public function ajout_produits_med(int $signalId, SignalRepository $signalRepo, Request $request, ManagerRegistry $doctrine, ?string $codeCIS = null): Response
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
            // Traitement si codeCIS est fourni (actuellement, seulement pour les substances médicinales)
            // Pour gérer les substances non-médicinales ici, il faudrait modifier la route pour inclure
            // un identifiant de type (ex: Medic, NonMedic) et un identifiant générique (ex: codeCIS, cas_id)
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
            $produit->setTypeSubstance('Medic');
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
            $produit->setNomProduit($vuUtil[0]->getNomProduit());
            
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
            $produit->setTypeSubstance('SaisieManuelle');
        }


        $produit->setCreatedAt(new \DateTimeImmutable());
        $produit->setUpdatedAt(new \DateTimeImmutable());
        $produit->setUserCreate($userName);
        $produit->setUserModif($userName);


        $form = $this->createForm(
                        ProduitsMedType::class, 
                        $produit, [
                            'show_delete_button' => false, // Affiche le bouton de suppression pour les produits de saisie manuelle
                        ]);
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
                    return $this->redirectAfterProductModification($request, $signalId);
                }
            }
            // if ($form->get('reset')->isClicked()) {
            //     return $this->redirectToRoute('app_creation_produits', ['signalId' => $signalId]);
            // }
            if ($form->get('annulation')->isClicked()) {
                return $this->redirectAfterProductModification($request, $signalId);
            }
        }



        return $this->render('gestion_produits/ajout_produit_med_recherche.html.twig', [
            'form' => $form->createView(),
            'medics' => $medics ?? [],
            'NbMedics' => $NbMedics ?? 0,
            'signalId' => $signalId,
        ]);
    }


    #[Route('/signal/{signalId}/ajout_produit_non_med/{SubSIMADId}', name: 'app_ajout_produit_non_med', defaults: ['SubSIMADId' => null])]
    public function ajout_produits_non_med(int $signalId, SignalRepository $signalRepo, Request $request, ManagerRegistry $doctrine, ?int $SubSIMADId = null): Response
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

        if ($SubSIMADId) {
        //     // Traitement si codeCIS est fourni (actuellement, seulement pour les substances médicinales)
        //     // Pour gérer les substances non-médicinales ici, il faudrait modifier la route pour inclure
        //     // un identifiant de type (ex: Medic, NonMedic) et un identifiant générique (ex: codeCIS, cas_id)
            $subSIMAD = $doctrine
            ->getRepository(SubSIMAD::class, 'codex')
            ->find($SubSIMADId);

            $subSIMAD_PT = $doctrine
            ->getRepository(SubSIMAD::class, 'codex')
            ->findOneBy(['unii_id' => $subSIMAD->getUniiId(), 'topproductname' => 'PT']);

            $produit = new Produits();
            // 3. Remplir l'entité avec les données
            $produit->setSignalLie($signal);

            $produit->setTypeSubstance('NonMedic');
            $produit->setDenomination($subSIMAD->getProductname());  // donnée saisie
            $produit->setDci($subSIMAD_PT?->getProductname()); // PT
            $produit->setProductFamily($subSIMAD->getProductfamily());
            $produit->setTopProductName($subSIMAD->getTopproductname());
            $produit->setUniiId($subSIMAD->getUniiId());
            $produit->setCasId($subSIMAD->getCasId());
            
            $produit->setCreatedAt(new \DateTimeImmutable());
            $produit->setUpdatedAt(new \DateTimeImmutable());
            $produit->setUserCreate($userName);
            $produit->setUserModif($userName);

        }

        $form = $this->createForm(
                        ProduitsNonMedType::class, 
                        $produit, [
                            'show_delete_button' => false, // Affiche le bouton de suppression pour les produits de saisie manuelle
                        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($form->get('validation')->isClicked()) {
                if ($form->isValid()) {

                    $data = $form->getData();
                    $em = $doctrine->getManager();
                    $em->persist($produit);
                    $em->flush();
                    return $this->redirectAfterProductModification($request, $signalId);
                }
            }

            if ($form->get('annulation')->isClicked()) {
                return $this->redirectAfterProductModification($request, $signalId);
            }
        }

        return $this->render('gestion_produits/ajout_produit_non_med_recherche.html.twig', [
            'form' => $form->createView(),
            'medics' => $medics ?? [],
            'NbMedics' => $NbMedics ?? 0,
            'signalId' => $signalId,
        ]);
    }

    #[Route('/signal/{signalId}/produits/ajout_produits_masse', name: 'app_ajout_produits_masse', methods: ['POST'])]
    public function ajoutProduitsMasse(
        Request $request,
        #[MapEntity(id: 'signalId')] Signal $signal, // Le ParamConverter trouve le signal via {signalId}
        ManagerRegistry $doctrine
    ): JsonResponse {

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

        $content = $request->getContent();
        // $this->logger->info('GestionProduitsController: Données brutes reçues : ' . $content);

        $data = json_decode($content, true);
        if (!$data) {
            $this->logger->error('GestionProduitsController: Échec du décodage JSON');
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $selectedMeds = $data['meds'] ?? [];
        $selectedNonMeds = $data['nonMeds'] ?? [];
        $addedCount = 0;

        
        $em = $doctrine->getManager();

        // Logique pour ajouter les médicaments (reprendre celle de 'app_ajout_produit_med')
        foreach ($selectedMeds as $codeCIS) {
            // TODO: Implémenter la logique de création de produit à partir du codeCIS
            // 1. Récupérer les données de la base Codex
            
            $vuUtil = $doctrine
                ->getRepository(VUUtil::class, 'codex')
                ->findByCodeCIS($codeCIS);
            [$DCI, $dosage] = $doctrine
                ->getRepository(SAVU::class, 'codex')
                ->findByCodeCIS_DCI_Dosage($codeCIS);
            $voieAdmin = $doctrine
                ->getRepository(SAVU::class, 'codex')
                ->findByCodeCIS_VoieAdmin($codeCIS);

            // 2. Créer une nouvelle entité `Produit`
            $produit = new Produits();
            // 3. Remplir l'entité avec les données
            $produit->setTypeSubstance('Medic');
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
            $produit->setNomProduit($vuUtil[0]->getNomProduit());
            
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

            $produit->setCreatedAt(new \DateTimeImmutable());
            $produit->setUpdatedAt(new \DateTimeImmutable());
            $produit->setUserCreate($userName);
            $produit->setUserModif($userName);

            // 4. Persister l'entité
            $em->persist($produit);
            $addedCount++;
        }

        // Logique pour ajouter les substances non-médicamenteuses
        foreach ($selectedNonMeds as $subSIMADId) {
            // TODO: Implémenter la logique de création de produit à partir de SubSIMADId


            // $this->logger->info(sprintf('ajoutProduitsMasse(): Début ajout pour le signal %s', $signal->getId()));


            // 1. Récupérer les données de la base Codex
            $subSIMAD = $doctrine
            ->getRepository(SubSIMAD::class, 'codex')
            ->find($subSIMADId);

            // $this->logger->info(sprintf('ajoutProduitsMasse(): et pour la substance non-med %s', $subSIMAD->getId()));

            // $this->logger->info(sprintf('ajoutProduitsMasse(): uniid de la substance %s', $subSIMAD->getUniiId()));

            $subSIMAD_PT = $doctrine
            ->getRepository(SubSIMAD::class, 'codex')
            ->findOneBy(['unii_id' => $subSIMAD->getUniiId(), 'topproductname' => 'PT']);


            // $this->logger->info(sprintf('ajoutProduitsMasse(): Début ajout pour le signal %s et pour la substance non-med  %s', 
            //                 $signal->getId(), 
            //                 $subSIMADId));
            
            
            // 2. Créer une nouvelle entité `Produit`
            $produit = new Produits();
            // 3. Remplir l'entité avec les données
            $produit->setSignalLie($signal);

            $produit->setTypeSubstance('NonMedic');
            $produit->setDenomination($subSIMAD->getProductname());  // donnée saisie
            $produit->setDci($subSIMAD_PT?->getProductname()); // PT
            $produit->setProductFamily($subSIMAD->getProductfamily());
            $produit->setTopProductName($subSIMAD->getTopproductname());
            $produit->setUniiId($subSIMAD->getUniiId());
            $produit->setCasId($subSIMAD->getCasId());
            
            $produit->setCreatedAt(new \DateTimeImmutable());
            $produit->setUpdatedAt(new \DateTimeImmutable());
            $produit->setUserCreate($userName);
            $produit->setUserModif($userName);
            // 4. Persister l'entité
            $em->persist($produit);
            // $newProduit = new Produits();
            // ...
            // $newProduit->setSignalLie($signal);
            // $entityManager->persist($newProduit);
            $addedCount++;
        }

        if ($addedCount > 0) {
            $em->flush();
            $this->addFlash('success', $addedCount . ' produit(s)/substance(s) ont été ajouté(s) au signal. Pensez à compléter les informations manquantes.');
            $this->logger->info('GestionProduitsController: ' . $addedCount . ' produits ajoutés.');
        }

        $redirectUrl = $this->generateUrl('app_signal_modif', ['signalId' => $signal->getId()]);
        $this->logger->info('GestionProduitsController: Redirection vers ' . $redirectUrl);

        // On renvoie l'URL de redirection au JS
        return $this->json([
            'redirectTo' => $redirectUrl
        ]);
    }


    /**
     * Regroupe les médicaments par codeCIS à partir des données du formulaire
     */
    private function getMedics(ManagerRegistry $doctrine, array $data): array
    {
        $medicinalResults = $doctrine->getRepository(VUUtil::class, 'codex')->findByDenoOrBySub($data['dci'], $data['denomination']);
        $nonMedicinalResults = $doctrine->getRepository(SubSIMAD::class, 'codex')->findByDenoOrBySub($data['dci'], $data['denomination']);

        $medics = [
            'SubMed' => [],
            'SubNonMed' => [],
        ];

        // Traitement des résultats médicinaux (groupés par codeCIS)
        $subMedGrouped = [];
        foreach ($medicinalResults as $row) {
            $cis = $row['codeCIS'];
            if (!isset($subMedGrouped[$cis])) {
                $subMedGrouped[$cis] = [
                    'id' => $row['id'],
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
                    'typeSubstance' => 'Medic',
                    'substances' => [],
                    'commercialise' => $this->codexPresentationRepository->auMoinsUnePresentationCommercialisee($row['codeVU']),
                ];
            }
            if (isset($row['nomSubstance']) && $row['nomSubstance'] !== null) {
                $subMedGrouped[$cis]['substances'][] = $row['nomSubstance'];
            }
        }
        $medics['SubMed'] = array_values($subMedGrouped);

        // Traitement des résultats non-médicinaux (groupés par leur ID unique)
        $subNonMedGrouped = [];
        foreach ($nonMedicinalResults as $row) {
            $id = $row['id'];
            if (!isset($subNonMedGrouped[$id])) {
                $subNonMedGrouped[$id] = $row;
                $subNonMedGrouped[$id]['typeSubstance'] = 'NonMedic';
            }
        }
        $medics['SubNonMed'] = array_values($subNonMedGrouped);

        $NbMedics = count($medics['SubMed']) + count($medics['SubNonMed']);
        return [$medics, $NbMedics];
    }

    private function redirectAfterProductModification(Request $request, int $signalId): Response
    {
        $session = $request->getSession();
        $returnToUrl = $session->get('return_to_after_product_creation');

        if ($returnToUrl) {
            $session->remove('return_to_after_product_creation');
            return $this->redirect($returnToUrl);
        }

        // Fallback redirection
        return $this->redirectToRoute('app_signal_modif', ['signalId' => $signalId]);
    }
}