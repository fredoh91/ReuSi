<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Entity\Signal;
use App\Entity\StatutSuivi;
use App\Entity\StatutSignal;
use Psr\Log\LoggerInterface;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
use App\Service\SuiviStatusService;
use App\Service\SignalStatusService;
use App\Form\SignalAvecSuiviInitialType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Model\SignalAvecSuiviInitialDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SignalDetailController extends AbstractController
{
    private $logger;
    private $kernel;
    
    public function __construct(LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    #[Route('/signal_new', name: 'app_signal_new', defaults: ['typeSignal' => 'signal'])]
    #[Route('/fait_marquant_new', name: 'app_fait_marquant_new', defaults: ['typeSignal' => 'fait_marquant'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SignalStatusService $signalStatusService,
        string $typeSignal
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        $userName = $user->getUserName();

        // Récupération des paramètres optionnels
        $reunionId = $request->query->get('reunionId');

        $dto = new SignalAvecSuiviInitialDTO();
        $dto->signal = new Signal();
        $dto->signal->setTypeSignal($typeSignal);
        $dto->suiviInitial = new Suivi();
        $dto->suiviInitial->setNumeroSuivi(0);
        
        // Pré-selection de la réunion si fournie
        if ($reunionId) {
            $reunion = $em->getRepository(ReunionSignal::class)->find($reunionId);
            if ($reunion) {
                $dto->suiviInitial->setReunionSignal($reunion);
            }
        }

        $dto->rddInitial = new ReleveDeDecision();
        $dto->rddInitial->setNumeroRDD(0);

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal(null, 200, 'DESC');
        
        // S'assurer que la réunion pré-sélectionnée est dans la liste des choix
        if ($dto->suiviInitial->getReunionSignal()) {
            $reunionSelectionnee = $dto->suiviInitial->getReunionSignal();
            $found = false;
            foreach ($date_reunion as $r) {
                if ($r->getId() === $reunionSelectionnee->getId()) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_unshift($date_reunion, $reunionSelectionnee);
            }
        }

        $form = $this->createForm(SignalAvecSuiviInitialType::class, $dto, [
            'reunions' => $date_reunion,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $signalForm = $form->get('signal');
            $routeSource = $request->query->get('routeSource', 'app_signal_liste');
            $reuSiId = $request->query->get('reuSiId');
            $tab = $request->query->get('tab');

            $redirectParams = [];
            if ($reuSiId) $redirectParams['reuSiId'] = $reuSiId;
            if ($tab) $redirectParams['tab'] = $tab;

            if ($signalForm->get('annulation')->isClicked()) {
                return $this->redirectToRoute($routeSource, $redirectParams);
            }

            if ($form->isValid()) {
                // For any valid submission (Validate, Add Product, etc.), we save the signal.
                $signal = $dto->signal;
                $suivi = $dto->suiviInitial;
                $rdd = $dto->rddInitial;
                
                $now = new \DateTimeImmutable();
                $signal->setCreatedAt($now)->setUserCreate($userName);

                $suiviInitial = $dto->suiviInitial;

                
                
                $statutSignalBrouillon = $signal->getStatutSignals()->first();
                if ($statutSignalBrouillon) {
                    $statutSignalBrouillon->setDateMiseEnPlace($now);
                    $statutSignalBrouillon->setDateDesactivation($now);
                    $statutSignalBrouillon->setStatutActif(false);
                    $statutSignalBrouillon->setCreatedAt($now);
                    $statutSignalBrouillon->setUpdatedAt($now);
                    $statutSignalBrouillon->setUserCreate($userName);
                    $statutSignalBrouillon->setUserModif($userName);
                    $em->persist($statutSignalBrouillon);
                    
                    $statutSignalEnCours = new StatutSignal();
                    if ($suiviInitial && $suiviInitial->getReunionSignal()) {
                        // On a une réunion de sélectionné
                        $dateReunion = $suiviInitial->getReunionSignal()->getDateReunion();
                        if($dateReunion <= $now && trim($rdd->getDescriptionRDD()) != '') {
                            // on a une date de réunion antérieure a la date du jour et une description de RDD remplie
                            $statutSignalEnCours->setLibStatut('presente');
                        } else {
                            $statutSignalEnCours->setLibStatut('prevu');
                        }
                    } else {
                        // On n'a pas une réunion de sélectionné
                        $statutSignalEnCours->setLibStatut('en_cours_de_creation');
                    }
                    $statutSignalEnCours->setDateMiseEnPlace($now);
                    $statutSignalEnCours->setStatutActif(true);
                    $statutSignalEnCours->setSignalLie($signal);
                    $statutSignalEnCours->setCreatedAt($now);
                    $statutSignalEnCours->setUpdatedAt($now);
                    $statutSignalEnCours->setUserCreate($userName);
                    $statutSignalEnCours->setUserModif($userName);
                    $em->persist($statutSignalEnCours);
                }
                $suivi->setSignalLie($signal)->setCreatedAt($now)->setUserCreate($userName);
                $rdd->setSignalLie($signal)->setCreatedAt($now)->setUserCreate($userName);
                $suivi->setRddLie($rdd);

                $signal->setUpdatedAt($now)->setUserModif($userName);
                $suivi->setUpdatedAt($now)->setUserModif($userName);

                $statutSuiviBrouillon = $suivi->getStatutSuivis()->first();
                if ($statutSuiviBrouillon) {
                    $statutSuiviBrouillon->setDateMiseEnPlace($now);
                    $statutSuiviBrouillon->setDateDesactivation($now);
                    $statutSuiviBrouillon->setStatutActif(false);
                    $statutSuiviBrouillon->setCreatedAt($now);
                    $statutSuiviBrouillon->setUpdatedAt($now);
                    $statutSuiviBrouillon->setUserCreate($userName);
                    $statutSuiviBrouillon->setUserModif($userName);
                    $em->persist($statutSuiviBrouillon);

                    $statutSuiviEnCours = new StatutSuivi();
                    if ($suiviInitial && $suiviInitial->getReunionSignal()) {
                        // On a une réunion de sélectionné
                        $statutSuiviEnCours->setLibStatut('prevu');
                    } else {
                        // On n'a pas une réunion de sélectionné
                        $statutSuiviEnCours->setLibStatut('en_cours_de_creation');
                    }
                    $statutSuiviEnCours->setDateMiseEnPlace($now);
                    $statutSuiviEnCours->setStatutActif(true);
                    $statutSuiviEnCours->setSuiviLie($suivi);
                    $statutSuiviEnCours->setCreatedAt($now);
                    $statutSuiviEnCours->setUpdatedAt($now);
                    $statutSuiviEnCours->setUserCreate($userName);
                    $statutSuiviEnCours->setUserModif($userName);
                    $em->persist($statutSuiviEnCours);
                }

                if($rdd) { $rdd->setUpdatedAt($now)->setUserModif($userName); }
                if ($suivi && $rdd) { $rdd->setReunionSignal($suivi->getReunionSignal()); }

                $em->persist($signal);
                $em->persist($suivi);
                $em->persist($rdd);
                $em->flush();

                // Now handle the specific button's action
                if ($signalForm->get('validation')->isClicked()) {
                    $this->addFlash('success', 'Signal créé avec succès.');
                    return $this->redirectToRoute($routeSource, $redirectParams);
                }

                if ($signalForm->has('ajout_produit') && $signalForm->get('ajout_produit')->isClicked()) {
                    $this->addFlash('info', 'Signal sauvegardé. Vous pouvez maintenant ajouter un produit.');
                    $returnUrl = $this->generateUrl('app_signal_modif', ['signalId' => $signal->getId()]);
                    $request->getSession()->set('return_to_after_product_creation', $returnUrl);
                    return $this->redirectToRoute('app_creation_produits', ['signalId' => $signal->getId()]);
                }
                 // ... handle other buttons like ajout_mesure, etc.
                
                if ($form->get('suivi')->get('ajout_mesure')->isClicked()) {

                    dd('coucou');

                    // On sauvegarde les données avant de rediriger
                    // $em->persist($dto->suivi);
                    // $em->persist($dto->rddLie);
                    // $em->flush();
                    return $this->redirectToRoute('app_creation_mesure', ['signalId' => $signalId, 'rddId' => $dto->rddLie->getId()]);
                }
            }
        }
        
        $isDateSuiviInitModifiable = true;

        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $dto->signal,
            'isCreationSignalMode' => true,
            'isDateSuiviInitModifiable' => $isDateSuiviInitModifiable,
            'lstProduits' => [],
            'lstRDD' => [],
            'latestRddId' => null,
            'isLatestRdd' => true,
            'lstSuivi' => [],
            'latestSuiviId' => null,
            'mesuresInitiales' => [],
        ]);
    }

    #[Route('/signal_modif/{signalId}', name: 'app_signal_modif', requirements: ['signalId' => '\d+'])]
    public function signal_modif(
        Request $request,
        EntityManagerInterface $em,
        SignalStatusService $signalStatusService,
        SuiviStatusService $suiviStatusService,
        int $signalId
    ): Response {
        $signal = $em->getRepository(Signal::class)->find($signalId);
        if (!$signal) {
            throw $this->createNotFoundException('Ce signal n\'existe pas');
        }

        $user = $this->getUser();
        if (!$user) { throw $this->createAccessDeniedException('Utilisateur non connecté.'); }
        $userName = $user->getUserName();

        $suiviInitial = $em->getRepository(Suivi::class)->findInitialForSignal($signal);
        if (!$suiviInitial) { /* ... handle missing initial suivi ... */ }

        $rddInitial = $em->getRepository(ReleveDeDecision::class)->findOneBy(['SignalLie' => $signal, 'NumeroRDD' => 0]);
        if (!$rddInitial && $suiviInitial && $suiviInitial->getRddLie()) {
            $rddInitial = $suiviInitial->getRddLie();
        }

        $dto = new SignalAvecSuiviInitialDTO();
        $dto->signal = $signal;
        $dto->suiviInitial = $suiviInitial;
        $dto->rddInitial = $rddInitial;

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 200, 'DESC');
        $reunionInitiale = $dto->suiviInitial ? $dto->suiviInitial->getReunionSignal() : null;

        if ($reunionInitiale && !in_array($reunionInitiale, $date_reunion)) {
            array_unshift($date_reunion, $reunionInitiale);
        }

        $lastStatutSignal = $signalStatusService->findLastStatutBySignal($signal) ?: null;

        if ($lastStatutSignal) {
            $dernierStatutSignal = $lastStatutSignal->getLibStatut();
        } else {
            $dernierStatutSignal = null;
        }

        
        $form = $this->createForm(SignalAvecSuiviInitialType::class, $dto, [ 'reunions' => $date_reunion, ]);
// dd($dto);
// pas de bug reunion signal à ce point 
        $form->handleRequest($request);         // le bug reunion signal apparait à ce point 

// debug [
// $em->flush();
// // dump(($suiviInitial));
// dd('function signal_modif_2');
//debug ]


        if ($form->isSubmitted() && $form->isValid()) {


            $signalForm = $form->get('signal');
            $routeSource = $request->query->get('routeSource', 'app_signal_liste');
            $reuSiId = $request->query->get('reuSiId', null);
            $tab = $request->query->get('tab', null);

            if ($signalForm->get('annulation')->isClicked()) {
                return $this->redirectToRoute($routeSource);
            }

            // For all other buttons, save the data
            $signal->setUpdatedAt(new \DateTimeImmutable())->setUserModif($userName);
            if ($suiviInitial) { $suiviInitial->setUpdatedAt(new \DateTimeImmutable())->setUserModif($userName); }
            if ($rddInitial) { 
                $rddInitial->setUpdatedAt(new \DateTimeImmutable())->setUserModif($userName);
                if ($suiviInitial) { $rddInitial->setReunionSignal($suiviInitial->getReunionSignal()); }
            }
            


            // Gestion des statutSignal et statutSuivi si nécessaire
            // $lastStatutSignal = $signalStatusService->findLastStatutBySignal($signal);
            if ($lastStatutSignal) {
                // dump ($lastStatutSignal);
                // On met a jour le statut du signal a "presente" si les conditions suivantes sont remplies :
                //    - le statut actuel est "prevu"
                //    - le suivi initial a une réunion associée et la date de cette réunion est passée
                //    - la description du relevé de décision initial est remplie
                if($lastStatutSignal->getLibStatut() == 'prevu' && $suiviInitial && $suiviInitial->getReunionSignal()) {
                    $dateReunion = $suiviInitial->getReunionSignal()->getDateReunion();
                    $now = new \DateTimeImmutable();
                    if($dateReunion <= $now && $rddInitial && trim($rddInitial->getDescriptionRDD()) != '') {
                        // On peut passer le statut a "presente"
                        // Désactiver l'ancien statut "prevu"
                        $lastStatutSignal->setStatutActif(false);
                        $lastStatutSignal->setDateDesactivation(new \DateTimeImmutable());
                        $lastStatutSignal->setUpdatedAt(new \DateTimeImmutable());
                        $lastStatutSignal->setUserModif($userName);
                        $em->persist($lastStatutSignal);

                        // Créer le nouveau statut "presente"
                        $nouveauStatutSignal = new StatutSignal();
                        $nouveauStatutSignal->setLibStatut('presente');
                        $nouveauStatutSignal->setDateMiseEnPlace(new \DateTimeImmutable());
                        $nouveauStatutSignal->setStatutActif(true);
                        $nouveauStatutSignal->setSignalLie($signal);
                        $nouveauStatutSignal->setCreatedAt(new \DateTimeImmutable());
                        $nouveauStatutSignal->setUserCreate($userName);
                        $nouveauStatutSignal->setUpdatedAt(new \DateTimeImmutable());
                        $nouveauStatutSignal->setUserModif($userName);

                        $em->persist($nouveauStatutSignal);
                    }
                }



            } else {
                $this->logger->error('Aucun statut signal trouvé pour le signal ID ' . $signal->getId());
            }
            $em->flush();
            
            // Handle specific button actions
            if ($signalForm->get('validation')->isClicked()) {
                $this->addFlash('success', 'Signal ' . $signalId . ' modifié.');

                // dump('reuSiId : ',$reuSiId);
                // dump('tab : ',$tab);
                // dd($routeSource);

                // il faut insérer la logique suivante :
                //   - si la date de reunion du suivi initial existe et que cette date est inférieure ou égal a la date du jour
                //     ET (
                //              que la description du RDD initial n'est pas vide
                //           OU qu'il y a au moins une mesure dans le RDD initial
                //        )
                //     ET que le statut du signal n'est pas "presente"
                //     ALORS on met le statut du signal à "presente"

                $dateReuSiInitiale = $suiviInitial?->getReunionSignal()?->getDateReunion();
                if ($dateReuSiInitiale && $dateReuSiInitiale <= new \DateTimeImmutable) {
                    $dateReuSiInitiale_estPassee = true;
                } else {
                    $dateReuSiInitiale_estPassee = false;
                }   


                $desciptionRDDInitiale = $rddInitial ? $rddInitial->getDescriptionRDD() : null;
                $mesuresRDDInitiales = $rddInitial ? $rddInitial->getMesuresRDDs()->toArray() : null;
                $rddRempli = (trim($desciptionRDDInitiale) != '') || (is_array($mesuresRDDInitiales) && count($mesuresRDDInitiales) > 0);


                $dernierStatutPremierSuivi = $em->getRepository(StatutSuivi::class)->findLastStatutBySuivi($suiviInitial->getId());
                $dernierStatutPremierSuivi_pasPresente = $dernierStatutPremierSuivi && $dernierStatutPremierSuivi->getLibStatut() != 'presente';

                if ($dateReuSiInitiale_estPassee && $rddRempli && $dernierStatutPremierSuivi_pasPresente) {
                    // on peut passer le statut du signal a "presente"
                    $suiviStatusService->updateStatutSuivi($suiviInitial, $userName, 'presente');
                    $signalStatusService->updateToPresenteIfNeeded($signal, $userName); 
                }

                // return $this->redirectToRoute($routeSource, ['signalId' => $signal->getId(), 'tab' => 'nouveaux-signaux']);
                return $this->redirectToRoute($routeSource, [
                    'reuSiId' => $reuSiId, 
                    'tab' => $tab,
                ]);
            }

            if ($signalForm->has('ajout_suivi') && $signalForm->get('ajout_suivi')->isClicked()) {
                $request->getSession()->set('return_to_after_product_creation', $request->getUri());
                return $this->redirectToRoute('app_creation_suivi', ['signalId' => $signal->getId()]);
            }

            if ($signalForm->has('ajout_produit') && $signalForm->get('ajout_produit')->isClicked()) {
                $request->getSession()->set('return_to_after_product_creation', $request->getUri());
                return $this->redirectToRoute('app_creation_produits', ['signalId' => $signal->getId()]);
            }


            if ($signalForm->has('ajout_mesure') && $signalForm->get('ajout_mesure')->isClicked()) {
                $request->getSession()->set('return_to_after_mesure_creation', $request->getUri());
                return $this->redirectToRoute('app_creation_mesure', ['signalId' => $signalId, 'rddId' => $dto->rddInitial->getId()]);
            }

        }

        $latestRDD = $em->getRepository(ReleveDeDecision::class)->findLatestForSignal($signal);

        $isDateSuiviInitModifiable = true;
        if ($suiviInitial) {
            $lastStatutSuivi = $em->getRepository(StatutSuivi::class)->findOneBy(['SuiviLie' => $suiviInitial], ['id' => 'DESC']);
            if ($lastStatutSuivi && $lastStatutSuivi->getLibStatut() === 'presente') {
                $isDateSuiviInitModifiable = false;
            } else {
                // si le suivi initial est lié à une réunion dont la date est passée, et que le suivi suivant a également une date passée, alors on considère que le suivi initial n'est plus modifiable
                $dateReuSiInitiale = $suiviInitial->getReunionSignal() ? $suiviInitial->getReunionSignal()->getDateReunion() : null;
                $suiviNumeroUn = $em->getRepository(Suivi::class)->findSuiviByIdSignalAndNumeroSuivi($signal, 1);
                $dateReuSiPremierSuivi = $suiviNumeroUn ? $suiviNumeroUn->getReunionSignal()->getDateReunion() : null;
                if ($dateReuSiInitiale && $dateReuSiInitiale <= new \DateTimeImmutable) {
                    if ($dateReuSiPremierSuivi && $dateReuSiPremierSuivi <= new \DateTimeImmutable) {
                        $isDateSuiviInitModifiable = false;
                    }
                }
            }
        }

        // dd('isDateSuiviInitModifiable', $isDateSuiviInitModifiable);

        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $signal,
            'isCreationSignalMode' => false,
            'isDateSuiviInitModifiable' => $isDateSuiviInitModifiable,
            'lstProduits' => $signal->getProduits(),
            'lstRDD' => $signal->getReleveDeDecision(),
            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
            'isLatestRdd' => $rddInitial && $latestRDD && $rddInitial->getId() === $latestRDD->getId(),
            'lstSuivi' => $em->getRepository(Suivi::class)->findForSignalExcludingInitial($signal),
            'latestSuiviId' => ($latestSuivi = $em->getRepository(Suivi::class)->findLatestForSignal($signal)) ? $latestSuivi->getId() : null,
            'mesuresInitiales' => $rddInitial ? $rddInitial->getMesuresRDDs()->toArray() : [],
            'dernierStatutSignal' => $dernierStatutSignal
        ]);
    }

    #[Route('/signal_detail/{signalId}', name: 'app_signal_detail', requirements: ['signalId' => '\d+'])]
    public function signal_detail(
        EntityManagerInterface $em,
        int $signalId
    ): Response {
        $signal = $em->getRepository(Signal::class)->find($signalId);
        if (!$signal) { throw $this->createNotFoundException('Ce signal n\'existe pas'); }
        return $this->render('signal/signal_detail.html.twig', [
            'signal' => $signal,
            'lstProduits' => $signal->getProduits(),
            'lstRDD' => $signal->getReleveDeDecision(),
        ]);
    }
}
