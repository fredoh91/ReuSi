<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Entity\Signal;
use Psr\Log\LoggerInterface;
use App\Entity\ReunionSignal;
use App\Form\SignalDetailType;
use App\Entity\ReleveDeDecision;
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
        EntityManagerInterface $em,
        string $typeSignal
    ): Response {

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        $userName = $user->getUserName();

        $signal = new Signal();
        $signal->setCreatedAt(new \DateTimeImmutable());
        $signal->setUpdatedAt(new \DateTimeImmutable());
        $signal->setUserCreate($userName);
        $signal->setUserModif($userName);
        $signal->setTypeSignal($typeSignal);

        $suivi = new Suivi();
        $suivi->setCreatedAt(new \DateTimeImmutable());
        $suivi->setUpdatedAt(new \DateTimeImmutable());
        $suivi->setUserCreate($userName);
        $suivi->setUserModif($userName);
        $suivi->setNumeroSuivi(0);
        $suivi->setSignalLie($signal);

        $rdd = new ReleveDeDecision();
        $rdd->setCreatedAt(new \DateTimeImmutable());
        $rdd->setUpdatedAt(new \DateTimeImmutable());
        $rdd->setUserCreate($userName);
        $rdd->setUserModif($userName);
        $rdd->setNumeroRDD(1);
        $rdd->setSignalLie($signal);

        $suivi->setRddLie($rdd);

        $em->persist($signal);
        $em->persist($suivi);
        $em->persist($rdd);
        $em->flush();

        return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
    }

    #[Route('/signal_modif/{signalId}', name: 'app_signal_modif', requirements: ['signalId' => '\d+'])]
    public function signal_modif(
        EntityManagerInterface $em,
        Request $request,
        int $signalId
    ): Response {
        $signal = $em->getRepository(Signal::class)->find($signalId);

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

        // On cherche le suivi initial (numeroSuivi = 0)
        $suiviInitial = $em->getRepository(Suivi::class)->findInitialForSignal($signal);
        if (!$suiviInitial) {
            // Normalement, la méthode `new` garantit qu'il existe.
            // En cas d'incohérence de données, on le crée à la volée pour rendre l'application plus robuste.
            $this->logger->warning(sprintf('Suivi initial manquant pour le signal ID %d. Création à la volée.', $signal->getId()));

            $suiviInitial = new Suivi();
            $suiviInitial->setSignalLie($signal);
            $suiviInitial->setNumeroSuivi(0);
            $suiviInitial->setUserCreate($userName);
            $suiviInitial->setUserModif($userName);
            $suiviInitial->setCreatedAt(new \DateTimeImmutable());
            $suiviInitial->setUpdatedAt(new \DateTimeImmutable());

            // On tente de retrouver le premier RDD (NumeroRDD = 1) pour recréer les liens
            $firstRdd = $em->getRepository(ReleveDeDecision::class)->findOneBy(['SignalLie' => $signal, 'NumeroRDD' => 1]);
            if ($firstRdd) {
                $suiviInitial->setRddLie($firstRdd);
                // Si le RDD est lié à une réunion, on lie aussi le suivi
                if ($firstRdd->getReunionSignal()) {
                    $suiviInitial->setReunionSignal($firstRdd->getReunionSignal());
                }
                $this->logger->info(sprintf('Le suivi initial recréé a été lié au RDD ID %d.', $firstRdd->getId()));
            }

            $em->persist($suiviInitial);
            $em->flush();
            $this->addFlash('warning', 'Le suivi initial manquant a été automatiquement recréé.');
        }

        // On cherche le RDD initial (NumeroRDD = 1)
        $rddInitial = $em->getRepository(ReleveDeDecision::class)->findOneBy(['SignalLie' => $signal, 'NumeroRDD' => 1]);
        if (!$rddInitial && $suiviInitial->getRddLie()) {
            // Cas où le RDD est lié au suivi mais pas directement au signal avec le bon numéro
            $rddInitial = $suiviInitial->getRddLie();
        }

        // On récupère les mesures associées au RDD initial s'il existe
        $mesuresInitiales = [];
        if ($rddInitial) {
            $mesuresInitiales = $rddInitial->getMesuresRDDs();
        }


        // $signal = new Signal();
        // $signal->setCreatedAt(new \DateTimeImmutable());
        $signal->setUpdatedAt(new \DateTimeImmutable());
        // $signal->setUserCreate($userName);
        $signal->setUserModif($userName);

        $lstProduits = $signal->getProduits();

        $lstSuivi = $em->getRepository(Suivi::class)->findForSignalExcludingInitial($signal);

        $latestSuivi = $em->getRepository(Suivi::class)->findLatestForSignal($signal);
        
        $lstRDD = $signal->getReleveDeDecision();

        // On récupère le RDD le plus récent pour le passer à la vue
        $latestRDD = $em->getRepository(ReleveDeDecision::class)->findLatestForSignal($signal);

        // On vérifie si le RDD initial est bien le RDD le plus récent.
        // On s'assure que les deux objets existent avant de comparer leurs IDs pour éviter les erreurs.
        $isLatestRdd = false;
        if ($rddInitial && $latestRDD && $rddInitial->getId() === $latestRDD->getId()) {
            $isLatestRdd = true;
        }

        // On récupère les réunions disponibles (non annulées et non liées à un autre RDD de ce signal)
        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 200, 'DESC');

        // On récupère la réunion actuellement liée au suivi initial
        $reunionInitiale = $suiviInitial->getReunionSignal();

        // Si une réunion est déjà liée, on s'assure qu'elle est dans la liste des choix
        if ($reunionInitiale) {
            $dejaDansLaListe = false;
            foreach ($date_reunion as $reunion) {
                if ($reunion->getId() === $reunionInitiale->getId()) {
                    $dejaDansLaListe = true;
                    break;
                }
            }
            // Si elle n'est pas dans la liste, on l'ajoute
            if (!$dejaDansLaListe) {
                // On l'ajoute au début pour qu'elle apparaisse en premier si le tri n'est pas refait
                array_unshift($date_reunion, $reunionInitiale);
            }
        }



        $routeSource = $request->query->get('routeSource', null);
        $routeParams = $request->query->all('params');
        $allowedRoutesSource = ['app_signal_liste', 'app_fait_marquant_liste', 'app_reunion_signal_liste'];

        // On crée notre DTO et on le remplit
        $dto = new SignalAvecSuiviInitialDTO();
        $dto->signal = $signal;
        $dto->suiviInitial = $suiviInitial;
        $dto->rddInitial = $rddInitial;


        // On crée le formulaire composite en lui passant le DTO
        // Et on lui dit d'utiliser `SignalDetailBtnProduitRDDType` pour la partie "signal"
        $form = $this->createForm(SignalAvecSuiviInitialType::class, $dto, [
            'reunions' => $date_reunion,
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Les boutons sont maintenant dans un sous-formulaire 'signal' qui est lui-même
            // dans le sous-formulaire 'signal' du formulaire principal.
            // La structure est $form->signal->signal->bouton
            $signalForm = $form->get('signal');
            if ($signalForm->get('annulation')->isClicked()) {
                // Annulation

                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('modif signal - 01 - bouton annulation');
                    $this->logger->info('modif signal - 01 - bouton annulation');
                }

                // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                if ($routeSource === 'app_reunion_signal_detail' && isset($routeParams['reuSiId'])) {
                    // On récupère le dernier suivi pour déterminer si c'est un "nouveau" ou un "suivi de"
                    $latestSuiviForTab = $em->getRepository(Suivi::class)->findLatestForSignal($signal);

                    // Logique pour déterminer l'ID de l'onglet
                    $tabId = '';
                    if ($dto->signal->getTypeSignal() === 'fait_marquant') {
                        $tabId = ($latestSuiviForTab && $latestSuiviForTab->getNumeroSuivi() > 0) ? 'suivis-fm' : 'nouveaux-fm';
                    } else {
                        $tabId = ($latestSuiviForTab && $latestSuiviForTab->getNumeroSuivi() > 0) ? 'suivis-signaux' : 'nouveaux-signaux';
                    }

                    // Construction de l'URL avec l'ancre
                    $url = $this->generateUrl('app_reunion_signal_detail', [
                        'reuSiId' => $routeParams['reuSiId'],
                        'tab' => $tabId,
                    ]); // . '#' . $tabId . ':suivi-' . $dto->suiviInitial->getId();

                    return $this->redirect($url);
                }

                if ($routeSource && in_array($routeSource, $allowedRoutesSource) && $routeSource !== 'app_reunion_signal_detail') {
                    return $this->redirectToRoute($routeSource);
                }
                return $this->redirectToRoute('app_signal_liste');
            }
            if ($signalForm->get('validation')->isClicked()) {
                // Le formulaire est lié au DTO, donc on vérifie la validité sur le DTO
                if ($form->isValid()) {
                    // Traitement de la validation

                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 02 - bouton validation');
                        $this->logger->info('modif signal - 02 - bouton validation');
                    }

                    // On s'assure que le RDD initial a la même réunion que le suivi initial
                    if ($dto->suiviInitial && $dto->rddInitial) {
                        $reunion = $dto->suiviInitial->getReunionSignal();
                        $dto->rddInitial->setReunionSignal($reunion);
                    }

                    // Symfony a mis à jour le DTO, on peut persister les entités qu'il contient
                    $em->persist($dto->signal);
                    $em->persist($dto->suiviInitial);
                    if ($dto->rddInitial) {
                        $em->persist($dto->rddInitial);
                    }

                    // $em->persist($signal);
                    $em->flush();

                    $this->addFlash('success', 'Le signal ' . $signalId . ' a bien été modifié');


                    // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                    if ($routeSource === 'app_reunion_signal_detail' && isset($routeParams['reuSiId'])) {
                        // On récupère le dernier suivi pour déterminer si c'est un "nouveau" ou un "suivi de"
                        $latestSuiviForTab = $em->getRepository(Suivi::class)->findLatestForSignal($signal);

                        // Logique pour déterminer l'ID de l'onglet
                        $tabId = '';
                        if ($dto->signal->getTypeSignal() === 'fait_marquant') {
                            $tabId = ($latestSuiviForTab && $latestSuiviForTab->getNumeroSuivi() > 0) ? 'suivis-fm' : 'nouveaux-fm';
                        } else {
                            $tabId = ($latestSuiviForTab && $latestSuiviForTab->getNumeroSuivi() > 0) ? 'suivis-signaux' : 'nouveaux-signaux';
                        }

                        // Construction de l'URL avec l'ancre
                        $url = $this->generateUrl('app_reunion_signal_detail', [
                            'reuSiId' => $routeParams['reuSiId'],
                            'tab' => $tabId,
                        ]); // . '#' . $tabId . ':suivi-' . $dto->suiviInitial->getId();

                        return $this->redirect($url);
                    }

                    if ($routeSource && in_array($routeSource, $allowedRoutesSource) && $routeSource !== 'app_reunion_signal_detail') {
                        return $this->redirectToRoute($routeSource);
                    }
                    return $this->redirectToRoute('app_signal_liste');
                } else {
                    // Formulaire invalide
                    
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 03 - formulaire invalide');
                        $this->logger->info('modif signal - 03 - formulaire invalide');
                    }
                }
            }

            // Les boutons sont maintenant dans le sous-formulaire 'signal'
            if ($signalForm->has('ajout_produit') && $signalForm->get('ajout_produit')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de produit
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 04 - bouton ajout produit');
                        $this->logger->info('modif signal - 04 - bouton ajout produit');
                    }
                    // On persist et flush pour que le signal soit à jour avant la redirection
                    $em->persist($dto->signal);
                    $em->persist($dto->suiviInitial);
                    if ($dto->rddInitial) {
                        $em->persist($dto->rddInitial);
                    }
                    $em->flush();

                    // Redirection vers la route pour creations des produits
                    return $this->redirectToRoute('app_creation_produits', ['signalId' => $signal->getId()]);
                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 05 - formulaire invalide');
                        $this->logger->info('modif signal - 05 - formulaire invalide');
                    }
                }
            }

            if ($signalForm->has('ajout_produit_saisie_manu') && $signalForm->get('ajout_produit_saisie_manu')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de produit
                    
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 06 - bouton ajout produit manuellement');
                        $this->logger->info('modif signal - 06 - bouton ajout produit manuellement');
                    }
                    // On persist et flush pour que le signal soit à jour avant la redirection
                    $em->persist($dto->signal);
                    $em->persist($dto->suiviInitial);
                    if ($dto->rddInitial) {
                        $em->persist($dto->rddInitial);
                    }
                    $em->flush();

                    // Redirection vers la route pour creations des produits dans un formulaire vide
                    return $this->redirectToRoute('app_ajout_produit', ['signalId' => $signal->getId(), 'codeCIS' => null]);
                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 07 - formulaire invalide');
                        $this->logger->info('modif signal - 07 - formulaire invalide');
                    }
                }
            }

            if ($signalForm->has('ajout_suivi') && $signalForm->get('ajout_suivi')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de suivi
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 10 - bouton ajout suivi');
                        $this->logger->info('modif signal - 10 - bouton ajout suivi');
                    }
                    // On persist et flush pour que le signal soit à jour avant la redirection
                    $em->persist($dto->signal);
                    $em->persist($dto->suiviInitial);
                    if ($dto->rddInitial) {
                        $em->persist($dto->rddInitial);
                    }
                    $em->flush();

                    // Redirection vers la route pour creations des produits
                    return $this->redirectToRoute('app_creation_suivi', ['signalId' => $signal->getId()]);
                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 11 - formulaire invalide');
                        $this->logger->info('modif signal - 11 - formulaire invalide');
                    }
                }
            }

            if ($signalForm->has('ajout_mesure') && $signalForm->get('ajout_mesure')->isClicked()) {
                // On ne valide pas le formulaire, on redirige directement
                // Mais on s'assure que le RDD initial existe avant de rediriger
                if ($dto->rddInitial) {
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 12 - bouton ajout mesure');
                        $this->logger->info('modif signal - 12 - bouton ajout mesure');
                    }
                    // On persist et flush pour que le signal soit à jour avant la redirection
                    $em->persist($dto->signal);
                    $em->persist($dto->suiviInitial);
                    $em->persist($dto->rddInitial);
                    $em->flush();

                    // Redirection vers la route pour la création d'une mesure, en liant au RDD initial
                    return $this->redirectToRoute('app_creation_mesure', ['signalId' => $signal->getId(), 'rddId' => $dto->rddInitial->getId()]);
                } else {
                    $this->addFlash('warning', 'Impossible d\'ajouter une mesure car le relevé de décision initial n\'a pas été trouvé.');
                }
            }
        }
        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $signal,
            'lstProduits' => $lstProduits,
            'lstRDD' => $lstRDD,
            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
            'isLatestRdd' => $isLatestRdd,
            'lstSuivi' => $lstSuivi,
            'latestSuiviId' => $latestSuivi ? $latestSuivi->getId() : null,
            'mesuresInitiales' => $mesuresInitiales,
        ]);
    }

    #[Route('/signal_detail/{signalId}', name: 'app_signal_detail', requirements: ['signalId' => '\d+'])]
    public function signal_detail(
        EntityManagerInterface $em,
        Request $request,
        int $signalId
    ): Response {
        $signal = $em->getRepository(Signal::class)->find($signalId);

        if (!$signal) {
            throw $this->createNotFoundException('Ce signal n\'existe pas');
        }

        $lstRDD = $signal->getReleveDeDecision();

        $lstProduits = $signal->getProduits();

        return $this->render('signal/signal_detail.html.twig', [
            'signal' => $signal,
            'lstProduits' => $lstProduits,
            'lstRDD' => $lstRDD,
        ]);
    }
}
