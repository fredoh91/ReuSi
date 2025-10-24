<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Entity\StatutSignal;
use App\Entity\ReunionSignal;
use App\Form\SuiviDetailType;
use App\Form\SignalSearchType;
use App\Form\SuiviAvecRddType;
use App\Entity\ReleveDeDecision;
use App\Form\Model\SuiviAvecRddDTO;
use App\Repository\SignalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class GestionSuivisController extends AbstractController
{

    #[Route('/signal/{signalId}/modif_suivi/{suiviId}', name: 'app_modif_suivi')]
    public function modif_suivi(
        int $signalId,
        int $suiviId,
        SignalRepository $signalRepo,
        Request $request,
        EntityManagerInterface $em,
        RouterInterface $router
    ): Response
    {

        $signal = $signalRepo->find($signalId);
        // Gérer le cas où le signal n'existe pas
        if (!$signal ) {
            throw $this->createNotFoundException('Le signal avec l\'id ' . $signalId . ' n\'existe pas.');
        }
        $suivi = $em->getRepository(Suivi::class)->find($suiviId);

        // Gérer le cas où le suivi n'existe pas
        if (!$suivi) {
            throw $this->createNotFoundException('Le suivi avec l\'id ' . $suiviId . ' n\'existe pas.');
        }

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 200, 'DESC');
        
        // On récupère la réunion actuellement liée au suivi
        $reunionActuelle = $suivi->getReunionSignal();
        
        // Si une réunion est déjà liée, on s'assure qu'elle est dans la liste des choix
        if ($reunionActuelle) {
            $dejaDansLaListe = false;
            foreach ($date_reunion as $reunion) {
                if ($reunion->getId() === $reunionActuelle->getId()) {
                    $dejaDansLaListe = true;
                    break;
                }
            }

            // Si elle n'est pas dans la liste, on l'ajoute
            if (!$dejaDansLaListe) {
                $date_reunion[] = $reunionActuelle;
            }

            // On trie le tableau par date de réunion (la plus récente en premier)
            // pour assurer un affichage cohérent et permettre la sélection par défaut.
            usort($date_reunion, function ($a, $b) {
                if (!$a->getDateReunion() || !$b->getDateReunion()) {
                    return 0;
                }
                return $b->getDateReunion() <=> $a->getDateReunion();
            });
        }

        // On recupère aussi les autres Suivis de ce signal pour les afficher dans la vue
        $autresSuivis = $em->getRepository(Suivi::class)->findBy(['SignalLie' => $signal], ['NumeroSuivi' => 'ASC']);

        // On récupère le suivi le plus récent pour le passer à la vue
        $latestSuivi = $em->getRepository(Suivi::class)->findLatestForSignal($signal);
        $isLatestSuivi = ($latestSuivi && $latestSuivi->getId() === $suivi->getId());

        // On récupère les mesures associées au RDD lié à ce suivi
        if ($suivi->getRddLie()) {
            $lstMesures = $suivi->getRddLie()->getMesuresRDDs();
        } else {
            // Si aucun RDD n'est lié, on peut initialiser une collection vide ou gérer le cas différemment
            $lstMesures = [];

            // Si on n'a pas de RDD lié, on en crée un nouveau
            // On regarde les autres RDD de ce signal et on récupère le numéro max
            $nextNumeroRDD = $em->getRepository(ReleveDeDecision::class)->donneNextNumeroRDD($signalId);

            $RDD = new ReleveDeDecision();
            $RDD->setSignalLie($signal);
            $RDD->setNumeroRDD($nextNumeroRDD);

            $RDD->setCreatedAt(new \DateTimeImmutable());
            $RDD->setUpdatedAt(new \DateTimeImmutable());
            $RDD->setUserCreate($userName);
            $RDD->setUserModif($userName);

            $suivi->setRddLie($RDD);
        }

        // --- NOUVELLE LOGIQUE DTO ---
        // 1. Créer le DTO et le peupler avec nos entités
        $dto = new SuiviAvecRddDTO();
        $dto->suivi = $suivi;
        $dto->rddLie = $suivi->getRddLie();


        // Création et gestion du formulaire
        // On lie le formulaire au DTO, pas directement aux entités
        $form = $this->createForm(SuiviAvecRddType::class, $dto, [
            'reunions' => $date_reunion,
        ]);

        $form->handleRequest($request); 

        if ($form->isSubmitted()) {

            $redirectRoute = $request->query->get('routeSource');
            $redirectParams = $request->query->all('params');
            $allowedRedirects = ['app_signal_liste','app_signal_modif','app_fait_marquant_liste'];
            // Les boutons sont maintenant dans le sous-formulaire 'suivi'
            if ($form->get('suivi')->get('annulation')->isClicked()) {
                // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                if ($redirectRoute && in_array($redirectRoute, $allowedRedirects)) {
                    $route = $router->getRouteCollection()->get($redirectRoute);
                    if ($route) {
                        $routeRequirements = $route->getRequirements();
                        $missingParams = array_diff(array_keys($routeRequirements), array_keys($redirectParams));
                        if (empty($missingParams)) {
                            return $this->redirectToRoute($redirectRoute, $redirectParams);
                        }
                    }
                }
            }

            if ($form->get('suivi')->get('ajout_mesure')->isClicked()) {
                // On sauvegarde les données avant de rediriger
                $em->persist($dto->suivi);
                $em->persist($dto->rddLie);
                $em->flush();
                return $this->redirectToRoute('app_creation_mesure', ['signalId' => $signalId, 'rddId' => $dto->rddLie->getId()]);
            }

            if ($form->get('suivi')->get('validation')->isClicked()) {
                $em->persist($dto->suivi);
                $em->persist($dto->rddLie);
                $em->flush();
                // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                if ($redirectRoute && in_array($redirectRoute, $allowedRedirects)) {
                    $route = $router->getRouteCollection()->get($redirectRoute);
                    if ($route) {
                        $routeRequirements = $route->getRequirements();
                        $missingParams = array_diff(array_keys($routeRequirements), array_keys($redirectParams));
                        if (empty($missingParams)) {
                            return $this->redirectToRoute($redirectRoute, $redirectParams);
                        }
                    }
                }
            }
        }

        // On trie les autres suivis par ordre décroissant pour l'affichage
        usort($autresSuivis, function ($a, $b) {
            return $b->getNumeroSuivi() <=> $a->getNumeroSuivi();
        });


        // return $this->render('gestion_suivis/suivi_modif.html.twig', [
        //     'signalId' => $signalId,
        //     'signal' => $signal,
        //     'suivi' => $suivi,
        // ]);

        return $this->render('gestion_suivis/suivi_modif.html.twig', [
            'signalId' => $signalId,
            'signal' => $signal,
            'autresSuivis' => $autresSuivis,
            'form' => $form->createView(),
            'rdd' => $dto->rddLie, // On passe le RDD depuis le DTO
            'typeModifCreation' => 'modification',
            'lstMesures' => $lstMesures, // On peut les passer à la vue pour un aperçu
            'isLatest' => $isLatestSuivi, // Un Suivi en création est considéré comme le plus récent pour l'UI
            'latestSuiviId' => $latestSuivi ? $latestSuivi->getId() : null,
        ]);
    }


    
    #[Route('/signal/{signalId}/creation_suivi', name: 'app_creation_suivi')]
    public function creation_suivi(
        int $signalId, 
        SignalRepository $signalRepo, 
        Request $request, 
        EntityManagerInterface $em
    ): Response
    {

        $signal = $signalRepo->find($signalId);

        // Gérer le cas où le signal n'existe pas
        if (!$signal ) {
            throw $this->createNotFoundException('Le signal avec l\'id ' . $signalId . ' n\'existe pas.');
        }

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 200, 'DESC');

        // On regarde les autres Suivis de ce signal et on récupère le numéro max
        $nextNumeroSuivi = $em->getRepository(Suivi::class)->donneNextNumeroSuivi($signalId);

        // On recupère aussi les autres Suivis de ce signal pour les afficher dans la vue
        $autresSuivis = $em->getRepository(Suivi::class)->findBy(['SignalLie' => $signal], ['NumeroSuivi' => 'ASC']);

        // On récupère le suivi le plus récent pour le passer à la vue
        $latestSuivi = $em->getRepository(Suivi::class)->findLatestForSignal($signal);

        $suivi = new Suivi();
        $suivi->setSignalLie($signal);
        $suivi->setNumeroSuivi($nextNumeroSuivi);

        $suivi->setCreatedAt(new \DateTimeImmutable());
        $suivi->setUpdatedAt(new \DateTimeImmutable());
        $suivi->setUserCreate($userName);
        $suivi->setUserModif($userName);

        // --- Logique de création du RDD associé ---
        // On regarde les autres RDD de ce signal et on récupère le numéro max
        $nextNumeroRDD = $em->getRepository(ReleveDeDecision::class)->donneNextNumeroRDD($signalId);

        // On recupère aussi les autres RDD de ce signal pour les afficher dans la vue
        $autresRDDs = $em->getRepository(ReleveDeDecision::class)->findBy(['SignalLie' => $signal], ['NumeroRDD' => 'ASC']);

        // On récupère le RDD le plus récent pour le passer à la vue
        $latestRDD = $em->getRepository(ReleveDeDecision::class)->findLatestForSignal($signal);

        $RDD = new ReleveDeDecision();
        $RDD->setSignalLie($signal);
        $RDD->setNumeroRDD($nextNumeroRDD);

        $RDD->setCreatedAt(new \DateTimeImmutable());
        $RDD->setUpdatedAt(new \DateTimeImmutable());
        $RDD->setUserCreate($userName);
        $RDD->setUserModif($userName);

        // On lie le nouveau suivi au nouveau RDD
        $suivi->setRddLie($RDD);

        $nouvellesMesures = [];

        // On recupère toutes les anciennes mesures non-cloturées de ce pour les associer a ce nouveau RDD
        $mesuresNonCloturees = $em->getRepository(\App\Entity\MesuresRDD::class)->findBy([
            'SignalLie' => $signal,
            'DesactivateAt' => null,
        ]);

        foreach ($mesuresNonCloturees as $mesure) {
            // Au lieu de cloner, on crée une nouvelle instance pour éviter les problèmes d'ID avec Doctrine
            $newMesure = new \App\Entity\MesuresRDD();
            $newMesure->setLibMesure($mesure->getLibMesure());
            $newMesure->setDetailCommentaire($mesure->getDetailCommentaire());
            $newMesure->setDateCloturePrev($mesure->getDateCloturePrev());

            $newMesure->setRddLie($RDD); // Associer la nouvelle mesure au nouveau RDD
            $newMesure->setSignalLie($signal); // Associer la nouvelle mesure au signal

            $newMesure->setCreatedAt(new \DateTimeImmutable());
            $newMesure->setUpdatedAt(new \DateTimeImmutable());
            $newMesure->setUserCreate($userName);
            $newMesure->setUserModif($userName);

            $nouvellesMesures[] = $newMesure;
        }

        // --- NOUVELLE LOGIQUE DTO (identique à modif_suivi) ---
        $dto = new SuiviAvecRddDTO();
        $dto->suivi = $suivi;
        $dto->rddLie = $RDD;

        // Création et gestion du formulaire
        // On utilise SuiviAvecRddType et le DTO pour être cohérent avec la modification
        $form = $this->createForm(SuiviAvecRddType::class, $dto, [
            'reunions' => $date_reunion,
            // On passe des options spécifiques pour le sous-formulaire RDD
            'rdd_options' => [
                'required_fields' => [
                    'DescriptionRDD' => false,
                    'PassageCTP' => false,
                    'PassageRSS' => false,
                ]
            ]
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('suivi')->get('annulation')->isClicked()) {
                return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
            }

            if ($form->get('suivi')->get('validation')->isClicked()) {
                // Validation spécifique pour la date de réunion
                $reunionSelectionnee = $form->get('suivi')->get('reunionSignal')->getData();
                if (!$reunionSelectionnee) {
                    $form->get('suivi')->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Ce champ est obligatoire.'));
                    $this->addFlash('error', 'Veuillez sélectionner une date de réunion avant de valider le formulaire.');
                }

                if ($form->isValid()) {
                    // On vérifie que la réunion n'est pas déjà utilisée par un autre suivi/RDD de ce signal
                    $rddExistante = $em->getRepository(ReleveDeDecision::class)->findOneBySignalAndReunionExcludingRdd($signal, $reunionSelectionnee, null);
                    if ($rddExistante) {
                        $form->get('suivi')->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Cette réunion est déjà liée à un autre RDD/Suivi de ce signal.'));
                        $this->addFlash('error', 'Cette date de réunion (' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ') existe déjà pour un autre suivi de ce signal. Veuillez en choisir une autre.');
                    } else {
                        // --- Début de la logique de persistance ---

                        // Lier la réunion sélectionnée au Suivi et au RDD
                        $suivi->setReunionSignal($reunionSelectionnee);
                        $RDD->setReunionSignal($reunionSelectionnee);
                        $signal->addReunionSignal($reunionSelectionnee);

                        // Gestion du statut du signal (passage à "en_cours")
                        $statutActif = $em->getRepository(StatutSignal::class)->findOneBy(['SignalLie' => $signal, 'StatutActif' => true]);
                        if ($statutActif) {
                            $statutActif->setStatutActif(false);
                            $statutActif->setDateDesactivation(new \DateTimeImmutable());
                            $em->persist($statutActif);
                        }
                        $nouveauStatut = new StatutSignal();
                        $nouveauStatut->setLibStatut('en_cours');
                        $nouveauStatut->setDateMiseEnPlace(new \DateTimeImmutable());
                        $nouveauStatut->setStatutActif(true);
                        $nouveauStatut->setSignalLie($signal);
                        $em->persist($nouveauStatut);
                        $signal->addStatutSignal($nouveauStatut);

                        // Persistance des nouvelles mesures dupliquées
                        foreach ($nouvellesMesures as $newMesure) {
                            $em->persist($newMesure);
                        }

                        // Clôture des anciennes mesures
                        foreach ($mesuresNonCloturees as $ancienneMesure) {
                            $ancienneMesure->setDesactivateAt(new \DateTimeImmutable());
                            $ancienneMesure->setDateClotureEffective(\DateTimeImmutable::createFromMutable($reunionSelectionnee->getDateReunion()));
                            $ancienneMesure->setUpdatedAt(new \DateTimeImmutable());
                            $ancienneMesure->setUserModif($userName);
                            $em->persist($ancienneMesure);
                        }

                        // Persistance du nouveau RDD et du nouveau Suivi
                        $em->persist($RDD);
                        $em->persist($suivi);
                        $em->persist($signal);

                        $em->flush();

                        $this->addFlash('success', 'Le nouveau suivi et son relevé de décision ont bien été créés pour la date du ' . $reunionSelectionnee->getDateReunion()->format('d/m/Y'));

                        return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                    }
                }
            }
        }

        return $this->render('gestion_suivis/suivi_modif.html.twig', [
            'signalId' => $signalId,
            'signal' => $signal,
            'autresSuivis' => $autresSuivis,
            'form' => $form->createView(),
            'rdd' => $dto->rddLie, // On passe le RDD depuis le DTO
            'typeModifCreation' => 'creation',
            'lstMesures' => $nouvellesMesures, // On peut les passer à la vue pour un aperçu
            'isLatest' => true, // Un Suivi en création est considéré comme le plus récent pour l'UI
            'latestSuiviId' => $latestSuivi ? $latestSuivi->getId() : null,
        ]);


    }

    #[Route('/reunion/{reunionId}/search-for-suivi/{type}', name: 'app_reunion_search_for_suivi', requirements: ['type' => 'signal|fait_marquant'])]
    public function searchSignalForSuivi(
        int $reunionId,
        string $type,
        Request $request,
        EntityManagerInterface $em,
        SignalRepository $signalRepository,
        PaginatorInterface $paginator
    ): Response {
        $reunion = $em->getRepository(ReunionSignal::class)->find($reunionId);
        if (!$reunion) {
            throw $this->createNotFoundException('Réunion non trouvée.');
        }

        $form = $this->createForm(SignalSearchType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        $criteria = $form->isSubmitted() && $form->isValid() ? $form->getData() : [];

        $queryBuilder = $signalRepository->findForSuiviAddition($type, $criteria, $reunion);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        $currentTab = $request->query->get('tab', 'nouveaux-signaux'); // Récupère le paramètre 'tab' de l'URL

        return $this->render('gestion_suivis/search_for_suivi.html.twig', [
            'reunion' => $reunion,
            'type' => $type,
            'form' => $form->createView(),
            'signaux' => $pagination,
            'currentTab' => $currentTab, // Passe le paramètre 'tab' à la vue
        ]);
    }

    #[Route('/reunion/{reunionId}/create-suivi-for/{signalId}', name: 'app_reunion_create_suivi')]
    public function createSuiviForReunion(
        int $reunionId,
        int $signalId,
        EntityManagerInterface $em,
        SignalRepository $signalRepo,
        Request $request
    ): Response {
        $reunion = $em->getRepository(ReunionSignal::class)->find($reunionId);
        if (!$reunion) {
            throw $this->createNotFoundException('Réunion non trouvée.');
        }

        $signal = $signalRepo->find($signalId);
        if (!$signal) {
            throw $this->createNotFoundException('Signal non trouvé.');
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        $userName = $user->getUserName();

        // --- Début de la logique de persistance (inspirée de creation_suivi) ---

        // Lier la réunion sélectionnée au Suivi et au RDD
        $nextNumeroSuivi = $em->getRepository(Suivi::class)->donneNextNumeroSuivi($signalId);
        $suivi = new Suivi();
        $suivi->setSignalLie($signal);
        $suivi->setNumeroSuivi($nextNumeroSuivi);
        $suivi->setCreatedAt(new \DateTimeImmutable());
        $suivi->setUpdatedAt(new \DateTimeImmutable());
        $suivi->setUserCreate($userName);
        $suivi->setUserModif($userName);
        $suivi->setReunionSignal($reunion);

        $nextNumeroRDD = $em->getRepository(ReleveDeDecision::class)->donneNextNumeroRDD($signalId);
        $RDD = new ReleveDeDecision();
        $RDD->setSignalLie($signal);
        $RDD->setNumeroRDD($nextNumeroRDD);
        $RDD->setCreatedAt(new \DateTimeImmutable());
        $RDD->setUpdatedAt(new \DateTimeImmutable());
        $RDD->setUserCreate($userName);
        $RDD->setUserModif($userName);
        $RDD->setReunionSignal($reunion);

        $suivi->setRddLie($RDD);
        $signal->addReunionSignal($reunion);

        // Gestion du statut du signal (passage à "en_cours")
        $statutActif = $em->getRepository(StatutSignal::class)->findOneBy(['SignalLie' => $signal, 'StatutActif' => true]);
        if ($statutActif) {
            $statutActif->setStatutActif(false);
            $statutActif->setDateDesactivation(new \DateTimeImmutable());
            $em->persist($statutActif);
        }
        $nouveauStatut = new StatutSignal();
        $nouveauStatut->setLibStatut('en_cours');
        $nouveauStatut->setDateMiseEnPlace(new \DateTimeImmutable());
        $nouveauStatut->setStatutActif(true);
        $nouveauStatut->setSignalLie($signal);
        $em->persist($nouveauStatut);
        $signal->addStatutSignal($nouveauStatut);

        // Duplication des mesures
        $mesuresNonCloturees = $em->getRepository(\App\Entity\MesuresRDD::class)->findBy([
            'SignalLie' => $signal,
            'DesactivateAt' => null,
        ]);

        foreach ($mesuresNonCloturees as $ancienneMesure) {
            $newMesure = new \App\Entity\MesuresRDD();
            $newMesure->setLibMesure($ancienneMesure->getLibMesure());
            $newMesure->setDetailCommentaire($ancienneMesure->getDetailCommentaire());
            $newMesure->setDateCloturePrev($ancienneMesure->getDateCloturePrev());
            $newMesure->setRddLie($RDD);
            $newMesure->setSignalLie($signal);
            $newMesure->setCreatedAt(new \DateTimeImmutable());
            $newMesure->setUpdatedAt(new \DateTimeImmutable());
            $newMesure->setUserCreate($userName);
            $newMesure->setUserModif($userName);
            $em->persist($newMesure);

            // Clôture de l'ancienne mesure
            $ancienneMesure->setDesactivateAt(new \DateTimeImmutable());
            $ancienneMesure->setDateClotureEffective(\DateTimeImmutable::createFromMutable($reunion->getDateReunion()));
            $ancienneMesure->setUpdatedAt(new \DateTimeImmutable());
            $ancienneMesure->setUserModif($userName);
            $em->persist($ancienneMesure);
        }

        // Persistance du nouveau RDD et du nouveau Suivi
        $em->persist($RDD);
        $em->persist($suivi);
        $em->persist($signal);

        $em->flush();

        $this->addFlash('success', sprintf(
            'Le suivi pour le signal "%s" a bien été ajouté à la réunion du %s.',
            $signal->getTitre(),
            $reunion->getDateReunion()->format('d/m/Y')
        ));

        // Récupérer l'onglet de la requête pour la redirection
        $tab = $request->query->get('tab', 'nouveaux-signaux');

        return $this->redirectToRoute('app_reunion_signal_detail', [
            'reuSiId' => $reunionId,
            'tab' => $tab,
        ]);
    }
}
