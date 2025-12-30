<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Entity\Signal;
use App\Entity\StatutSuivi;
use App\Entity\StatutSignal;
use Psr\Log\LoggerInterface;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
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

    #[Route('/signal_new', name: 'app_signal_new', defaults: ['typeSignal' => 'signal', 'routeSource' => null])]
    #[Route('/fait_marquant_new', name: 'app_fait_marquant_new', defaults: ['typeSignal' => 'fait_marquant', 'routeSource' => null])]
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

        $dto = new SignalAvecSuiviInitialDTO();
        $dto->signal = new Signal();
        $dto->signal->setTypeSignal($typeSignal);
        $dto->suiviInitial = new Suivi();
        $dto->suiviInitial->setNumeroSuivi(0);
        $dto->rddInitial = new ReleveDeDecision();
        $dto->rddInitial->setNumeroRDD(0);

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal(null, 200, 'DESC');
        
        $form = $this->createForm(SignalAvecSuiviInitialType::class, $dto, [
            'reunions' => $date_reunion,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $signalForm = $form->get('signal');
            $routeSource = $request->query->get('routeSource', 'app_signal_liste');

            if ($signalForm->get('annulation')->isClicked()) {
                return $this->redirectToRoute($routeSource);
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
                        $statutSignalEnCours->setLibStatut('prevu');
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
                    return $this->redirectToRoute($routeSource);
                }

                if ($signalForm->has('ajout_produit') && $signalForm->get('ajout_produit')->isClicked()) {
                    $this->addFlash('info', 'Signal sauvegardé. Vous pouvez maintenant ajouter un produit.');
                    $returnUrl = $this->generateUrl('app_signal_modif', ['signalId' => $signal->getId()]);
                    $request->getSession()->set('return_to_after_product_creation', $returnUrl);
                    return $this->redirectToRoute('app_creation_produits', ['signalId' => $signal->getId()]);
                }
                 // ... handle other buttons like ajout_mesure, etc.
            }
        }

        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $dto->signal,
            'isCreationMode' => true,
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
        
        $form = $this->createForm(SignalAvecSuiviInitialType::class, $dto, [ 'reunions' => $date_reunion, ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signalForm = $form->get('signal');
            $routeSource = $request->query->get('routeSource', 'app_signal_liste');

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
            
            $em->flush();

            // Handle specific button actions
            if ($signalForm->get('validation')->isClicked()) {
                $this->addFlash('success', 'Signal ' . $signalId . ' modifié.');
                return $this->redirectToRoute($routeSource);
            }

            if ($signalForm->has('ajout_produit') && $signalForm->get('ajout_produit')->isClicked()) {
                $request->getSession()->set('return_to_after_product_creation', $request->getUri());
                return $this->redirectToRoute('app_creation_produits', ['signalId' => $signal->getId()]);
            }

        }

        $latestRDD = $em->getRepository(ReleveDeDecision::class)->findLatestForSignal($signal);
        
        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $signal,
            'isCreationMode' => false,
            'lstProduits' => $signal->getProduits(),
            'lstRDD' => $signal->getReleveDeDecision(),
            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
            'isLatestRdd' => $rddInitial && $latestRDD && $rddInitial->getId() === $latestRDD->getId(),
            'lstSuivi' => $em->getRepository(Suivi::class)->findForSignalExcludingInitial($signal),
            'latestSuiviId' => ($latestSuivi = $em->getRepository(Suivi::class)->findLatestForSignal($signal)) ? $latestSuivi->getId() : null,
            'mesuresInitiales' => $rddInitial ? $rddInitial->getMesuresRDDs()->toArray() : [],
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
