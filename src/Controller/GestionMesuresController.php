<?php

namespace App\Controller;

use App\Entity\MesuresRDD;
use Psr\Log\LoggerInterface;
use App\Form\MesureDetailType;
use App\Repository\SignalRepository;
use App\Repository\MesuresRDDRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use App\Repository\ReleveDeDecisionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class GestionMesuresController extends AbstractController
{
    private $logger;
    private $kernel;
    
    public function __construct(LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    #[Route('/signal/{signalId}/RDD/{rddId}/creation_mesure', name: 'app_creation_mesure')]
        public function creation_mesure(
        int $signalId,
        int $rddId,
        SignalRepository $signalRepo,
        ReleveDeDecisionRepository $RDDRepo,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {

        
        $signal = $signalRepo->find($signalId);

        // Gérer le cas où le signal n'existe pas
        if (!$signal ) {
            throw $this->createNotFoundException('Le signal avec l\'id ' . $signalId . ' n\'existe pas.');
        }

        $RDD = $RDDRepo->find($rddId);

        if (!$RDD) {
            throw $this->createNotFoundException('Le relevé de décision avec l\'id ' . $rddId . ' n\'existe pas.');
        }

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $mesure = new MesuresRDD();

        $mesure->setSignalLie($signal);
        $mesure->setRddLie($RDD);

        $mesure->setCreatedAt(new \DateTimeImmutable());
        $mesure->setUpdatedAt(new \DateTimeImmutable());
        $mesure->setUserCreate($userName);
        $mesure->setUserModif($userName);


        $form = $this->createForm(MesureDetailType::class, $mesure);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                // Annulation

                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('creation mesure - 01 - bouton annulation');
                    $this->logger->info('creation mesure - 01 - bouton annulation');
                }

                return $this->redirectAfterModification($request, $signal->getId());
            }
            if ($form->get('validation')->isClicked()) {

                if ($form->isValid()) {
                    // Traitement de la validation
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('creation mesure - 02 - bouton validation');
                        $this->logger->info('creation mesure - 02 - bouton validation');
                    }

                    // Comme le champ 'LibMesure' n'est pas mappé, on le récupère manuellement
                    $listeMesureObject = $form->get('LibMesure')->getData();
                    if ($listeMesureObject) {
                        // On extrait la chaîne de caractères et on la définit sur l'entité MesuresRDD
                        $mesure->setLibMesure($listeMesureObject->getLibMesure());
                    }

                    $em->persist($mesure);
                    $em->flush();

                    $this->addFlash('success', 'La mesure ' . $mesure->getLibMesure() . ' a bien été créée.');

                    return $this->redirectAfterModification($request, $signal->getId());

                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('creation mesure - 03 - formulaire invalide');
                        $this->logger->info('creation mesure - 03 - formulaire invalide');
                    }
                }
            }
        }


        return $this->render('gestion_mesures/mesure_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $signal,
            'rdd' => $RDD,
            'TypeModifCreation' => 'creation',
        ]);
    }

    #[Route('/signal/{signalId}/RDD/{rddId}/modif_mesure/{mesureId}', name: 'app_modif_mesure')]
        public function modif_mesure(
        int $signalId,
        int $rddId,
        int $mesureId,
        SignalRepository $signalRepo,
        ReleveDeDecisionRepository $RDDRepo,
        MesuresRDDRepository $mesureRepo,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {

        
        $signal = $signalRepo->find($signalId);

        // Gérer le cas où le signal n'existe pas
        if (!$signal ) {
            throw $this->createNotFoundException('Le signal avec l\'id ' . $signalId . ' n\'existe pas.');
        }

        $RDD = $RDDRepo->find($rddId);

        if (!$RDD) {
            throw $this->createNotFoundException('Le relevé de décision avec l\'id ' . $rddId . ' n\'existe pas.');
        }

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $mesure = $mesureRepo->find($mesureId);

        if (!$mesure) {
            throw $this->createNotFoundException('La mesure avec l\'id ' . $mesureId . ' n\'existe pas.');
        }

        $mesure->setUpdatedAt(new \DateTimeImmutable());
        $mesure->setUserModif($userName);


        $form = $this->createForm(MesureDetailType::class, $mesure);

        // Pré-remplir le champ LibMesure qui n'est pas mappé
        $libelleMesure = $mesure->getLibMesure();
        if ($libelleMesure) {
            $listeMesuresRepo = $em->getRepository(\App\Entity\ListeMesures::class);
            $listeMesureObject = $listeMesuresRepo->findOneBy(['LibMesure' => $libelleMesure]);
            if ($listeMesureObject) {
                $form->get('LibMesure')->setData($listeMesureObject);
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                // Annulation

                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('creation mesure - 01 - bouton annulation');
                    $this->logger->info('creation mesure - 01 - bouton annulation');
                }

                return $this->redirectAfterModification($request, $signal->getId());
            }

            if ($form->get('validation')->isClicked()
                || $form->get('annulation_mesure')->isClicked()) {

                if ($form->isValid()) {
                    // Traitement de la validation
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('creation mesure - 02 - bouton validation');
                        $this->logger->info('creation mesure - 02 - bouton validation');
                    }

                    // Comme le champ 'LibMesure' n'est pas mappé, on le récupère manuellement
                    $listeMesureObject = $form->get('LibMesure')->getData();
                    if ($listeMesureObject) {
                        // On extrait la chaîne de caractères et on la définit sur l'entité MesuresRDD
                        $mesure->setLibMesure($listeMesureObject->getLibMesure());
                    }
                    // Si l'utilisateur souhaite annuler la mesure, vérifier que DetailCommentaire n'est pas vide
                    if ($form->get('annulation_mesure')->isClicked()) {
                        $detail = trim((string) $mesure->getDetailCommentaire());
                        if ($detail === '') {
                            // Empêcher la validation: ajouter une erreur au champ et laisser l'utilisateur corriger
                            $form->get('DetailCommentaire')->addError(new FormError('Le champ "Détail de la mesure" doit être rempli pour annuler la mesure.'));
                        } else {
                            // Procéder à l'annulation
                            $mesure->setStatut('annulee');
                            $this->addFlash('success', 'La mesure ' . $mesure->getLibMesure() . ' a bien été annulée.');
                            $em->persist($mesure);
                            $em->flush();
                            return $this->redirectAfterModification($request, $signal->getId());
                        }
                    } else {
                        // Modification simple
                        $this->addFlash('success', 'La mesure ' . $mesure->getLibMesure() . ' a bien été modifiée.');
                        $em->persist($mesure);
                        $em->flush();
                        return $this->redirectAfterModification($request, $signal->getId());
                    }

                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('creation mesure - 03 - formulaire invalide');
                        $this->logger->info('creation mesure - 03 - formulaire invalide');
                    }
                }
            }
        }


        return $this->render('gestion_mesures/mesure_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $signal,
            'rdd' => $RDD,
            'TypeModifCreation' => 'modification',
        ]);
    }

    #[Route('/mesure/{mesureId}/toggle_cloture', name: 'app_toggle_cloture_mesure')]
    public function toggle_cloture_mesure(
        int $mesureId,
        Request $request,
        MesuresRDDRepository $mesureRepo,
        EntityManagerInterface $em,
        RouterInterface $router
    ): Response {
        $mesure = $mesureRepo->find($mesureId);

        if (!$mesure) {
            throw $this->createNotFoundException('La mesure avec l\'id ' . $mesureId . ' n\'existe pas.');
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        $userName = $user->getUserName();

        $now = new \DateTimeImmutable();
        $mesure->setUpdatedAt($now);
        $mesure->setUserModif($userName);

        if ($mesure->getDesactivateAt()) {
            // La mesure est clôturée, on la rouvre
            $mesure->setDesactivateAt(null);
            $mesure->setDateClotureEffective(null);
            $this->addFlash('success', 'La mesure '. $mesure->getLibMesure() . ' a été rouverte.');
        } else {
            // La mesure est ouverte, on la clôture
            $mesure->setDesactivateAt($now);
            $reunionSignal = $mesure->getRddLie()->getReunionSignal();
            if ($reunionSignal && $reunionSignal->getDateReunion() instanceof \DateTime) {
                // Si une réunion est liée, on utilise sa date
                $mesure->setDateClotureEffective(\DateTimeImmutable::createFromMutable($reunionSignal->getDateReunion()));
            } else {
                // Sinon, on utilise la date du jour
                $mesure->setDateClotureEffective($now);
            }
            $this->addFlash('success', 'La mesure '. $mesure->getLibMesure() . ' a été clôturée.');
        }

        $em->flush();

        // Logique de redirection dynamique
        $redirectRoute = $request->query->get('redirect');
        $redirectParams = $request->query->all('params');

        // Vérifier si la route de redirection est valide pour éviter les redirections ouvertes
        $allowedRedirects = ['app_signal_modif', 'app_modif_suivi'];
        if ($redirectRoute && in_array($redirectRoute, $allowedRedirects)) {
            // Assurer que les paramètres nécessaires sont présents
            $routeParameters = $router->getRouteCollection()->get($redirectRoute)->getRequirements();
            $missingParams = array_diff(array_keys($routeParameters), array_keys($redirectParams));
            if (empty($missingParams)) {
                return $this->redirectToRoute($redirectRoute, $redirectParams);
            }
        }

        // Redirection par défaut si les paramètres ne sont pas bons
        return $this->redirectToRoute('app_signal_modif', [
            'signalId' => $mesure->getSignalLie()->getId(),
        ]);
    }

    private function redirectAfterModification(Request $request, int $signalId): Response
    {
        $session = $request->getSession();
        $returnToUrl = $session->get('return_to_after_measure_creation');

        if ($returnToUrl) {
            $session->remove('return_to_after_measure_creation');
            return $this->redirect($returnToUrl);
        }

        // Fallback redirection
        return $this->redirectToRoute('app_signal_modif', ['signalId' => $signalId]);
    }
}