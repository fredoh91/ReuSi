<?php

namespace App\Controller;

use App\Form\RDDDetailType;
use App\Entity\StatutSignal;
use Psr\Log\LoggerInterface;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
use App\Repository\SignalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class GestionRelevesDeDecisionController extends AbstractController
{
    private $logger;
    private $kernel;
    
    public function __construct(LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    #[Route('/signal/{signalId}/creation_RDD', name: 'app_creation_RDD')]
    public function creation_RDD(
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

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 100, 'DESC');

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


        $form = $this->createForm(RDDDetailType::class, $RDD, [
            'reunions' => $date_reunion,
        ]);
        $form->handleRequest($request);
        // $em->flush();
        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                // Annulation

                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('creation RDD - 01 - bouton annulation');
                    $this->logger->info('creation RDD - 01 - bouton annulation');
                }

                return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
            }
            if ($form->get('validation')->isClicked()) {
                if (!$form->get('reunionSignal')->getData()) {
                    $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Ce champ est obligatoire.'));
                    // affichage d'un message flash
                    $this->addFlash('error', 'Veuillez sélectionner une date de réunion avant de valider le formulaire.');
                }
                if ($form->isValid()) {
                    // Traitement de la validation
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('creation RDD - 02 - bouton validation');
                        $this->logger->info('creation RDD - 02 - bouton validation');
                    }
                    // avant de faire le persist, on vérifie que la date de la réunion selectionnée n'est pas déjà liée à un autre RDD de ce signal
                    $reunionSelectionnee = $form->get('reunionSignal')->getData();                    $rddExistante = $em->getRepository(ReleveDeDecision::class)->findOneBySignalAndReunionExcludingRdd($signal, $reunionSelectionnee, null);
                    if ($rddExistante) {
                        $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Cette réunion est déjà liée à un autre RDD de ce signal.'));
                        // affichage d'un message flash
                        $this->addFlash('error', 'Cette date de réunion (' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ') existe déjà pour un autre RDD de ce signal. Veuillez en choisir une autre date.');
                        return $this->render('gestion_releves_de_decision/RDD_modif.html.twig', [
                            'signalId' => $signalId,
                            'signal' => $signal,
                            'autresRDDs' => $autresRDDs,
                            'form' => $form->createView(),
                            'TypeModifCreation' => 'creation',
                            'lstMesures' => $nouvellesMesures ? $nouvellesMesures : [], // On peut les passer à la vue pour un aperçu
                            'isLatestRdd' => true, // Un RDD en création est considéré comme le plus récent pour l'UI
                            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
                        ]);
                    } else {
                        
                        
                        $RDD->setReunionSignal($reunionSelectionnee);
                        $signal->addReunionSignal($reunionSelectionnee); // Met à jour la relation ManyToMany dans l'entité Signal

                        $StatutSignal_ancien = $em->getRepository(StatutSignal::class)->findOneBySomeIdAndActif($signalId);
                        if ($StatutSignal_ancien) {
                            // On désactive l'ancien statut
                            $StatutSignal_ancien->setStatutActif(false);
                            $StatutSignal_ancien->setDateDesactivation(new \DateTimeImmutable());
                            $em->persist($StatutSignal_ancien);

                            // On crée le nouveau statut
                            $StatutSignal_nouveau = new StatutSignal();
                            $StatutSignal_nouveau->setLibStatut('en_cours');
                            $StatutSignal_nouveau->setDateMiseEnPlace(new \DateTimeImmutable());
                            $StatutSignal_nouveau->setStatutActif(true);
                            $StatutSignal_nouveau->setSignalLie($signal);

                            $em->persist($StatutSignal_nouveau);

                            // On met à jour le signal avec le nouveau statut
                            $signal->addStatutSignal($StatutSignal_nouveau);
                        } else {

                            //$this->addFlash('error', 'Aucun statut actif trouvé pour ce signal. Veuillez vérifier les statuts du signal avant de créer un RDD.');

                            // il n'y a pas d'autre RDD avec statut actif, on crée le premier
                            $StatutSignal_nouveau = new StatutSignal();
                            $StatutSignal_nouveau->setLibStatut('en_cours');
                            $StatutSignal_nouveau->setDateMiseEnPlace(new \DateTimeImmutable());
                            $StatutSignal_nouveau->setStatutActif(true);
                            $StatutSignal_nouveau->setSignalLie($signal);

                            $em->persist($StatutSignal_nouveau);

                            // On met à jour le signal avec le nouveau statut
                            $signal->addStatutSignal($StatutSignal_nouveau);

                            // return $this->render('gestion_releves_de_decision/RDD_modif.html.twig', [
                            //     'signalId' => $signalId,
                            //     'signal' => $signal,
                            //     'autresRDDs' => $autresRDDs,
                            //     'form' => $form->createView(),
                            //     'TypeModifCreation' => 'creation',
                            //     'lstMesures' => $nouvellesMesures ? $nouvellesMesures : [], // On peut les passer à la vue pour un aperçu
                            //     'isLatestRdd' => true, // Un RDD en création est considéré comme le plus récent pour l'UI
                            //     'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
                            // ]);
                        }

                        // On persiste les nouvelles mesures ici, uniquement en cas de validation ...
                        foreach ($nouvellesMesures as $newMesure) {
                            $em->persist($newMesure);
                        }
                        // ... et on clorure les anciennes mesures
                        foreach ($mesuresNonCloturees as $ancienneMesure) {
                            $ancienneMesure->setDesactivateAt(new \DateTimeImmutable());
                            $ancienneMesure->setDateClotureEffective(\DateTimeImmutable::createFromMutable($reunionSelectionnee->getDateReunion()));
                            $ancienneMesure->setUpdatedAt(new \DateTimeImmutable());
                            $ancienneMesure->setUserModif($userName);
                            $em->persist($ancienneMesure);
                        }

                        $em->persist($RDD);
                        $em->persist($signal);
                        $em->flush();

                        $this->addFlash('success', 'Le relevé de décision pour la date ' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ' a bien été créé');
                    }

                    return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);

                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('creation RDD - 03 - formulaire invalide');
                        $this->logger->info('creation RDD - 03 - formulaire invalide');
                    }
                }
            }
        }

        return $this->render('gestion_releves_de_decision/RDD_modif.html.twig', [
            'signalId' => $signalId,
            'signal' => $signal,
            'autresRDDs' => $autresRDDs,
            'form' => $form->createView(),
            'TypeModifCreation' => 'creation',
            'lstMesures' => $nouvellesMesures, // On peut les passer à la vue pour un aperçu
            'isLatestRdd' => true, // Un RDD en création est considéré comme le plus récent pour l'UI
            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
        ]);
    }

    #[Route('/signal/{signalId}/modif_RDD/{rddId}', name: 'app_modif_RDD')]
    public function modif_RDD(
        int $signalId,
        int $rddId,
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

        $RDD = $em->getRepository(ReleveDeDecision::class)->find($rddId);

        if (!$RDD) {
            throw $this->createNotFoundException('Le relevé de décision avec l\'id ' . $rddId . ' n\'existe pas.');
        }
        
        // Déterminer si le RDD actuel est le plus récent pour ce signal
        $latestRDD = $em->getRepository(ReleveDeDecision::class)->findLatestForSignal($signal);
        $isLatestRdd = ($latestRDD && $latestRDD->getId() === $RDD->getId());


        $lstMesures = $RDD->getMesuresRDDs();

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 100);

        // on ajoute l'entité réunion liée à ce RDD pour qu'elle puisse être sélectionnée dans le formulaire
        // if ( $RDD->getReunionSignal()) {
        //     $date_reunion[] = $RDD->getReunionSignal();
        // }

        $reunionActuelle = $RDD->getReunionSignal();
        // On vérifie si une réunion est déjà liée à notre RDD
        if ($reunionActuelle) {
            // On parcourt la liste pour vérifier si elle y est déjà
            $dejaDansLaListe = false;
            foreach ($date_reunion as $reunion) {
                // On compare les IDs pour être absolument certain
                if ($reunion->getId() === $reunionActuelle->getId()) {
                    $dejaDansLaListe = true;
                    break;
                }
            }

            // Si elle n'a pas été trouvée dans la liste, on l'ajoute
            if (!$dejaDansLaListe) {
                $date_reunion[] = $reunionActuelle;
            }
        }

        $RDD->setUpdatedAt(new \DateTimeImmutable());
        $RDD->setUserModif($userName);

        $form = $this->createForm(RDDDetailType::class, $RDD, [
            'reunions' => $date_reunion,
        ]);

        $autresRDDs = $em->getRepository(ReleveDeDecision::class)->findBy(['SignalLie' => $signal], ['NumeroRDD' => 'ASC']);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {

                // Annulation
                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('modif RDD - 01 - bouton annulation');
                    $this->logger->info('modif RDD - 01 - bouton annulation');
                }

                return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
            }
            if ($form->get('validation')->isClicked()) {
                if (!$form->get('reunionSignal')->getData()) {
                    $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Ce champ est obligatoire.'));
                    // affichage d'un message flash
                    $this->addFlash('error', 'Veuillez sélectionner une date de réunion avant de valider le formulaire.');
                }
                if ($form->isValid()) {

                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif RDD - 02 - bouton validation');
                        $this->logger->info('modif RDD - 02 - bouton validation');
                    }

                    // avant de faire le persist, on vérifie que la date de la réunion selectionnée n'est pas déjà liée à un autre RDD de ce signal
                    $reunionSelectionnee = $form->get('reunionSignal')->getData();
                    $rddExistante = $em->getRepository(ReleveDeDecision::class)->findOneBySignalAndReunionExcludingRdd($signal, $reunionSelectionnee, $rddId);
                    if ($rddExistante) {
                        $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Cette réunion est déjà liée à un autre RDD de ce signal.'));
                        // affichage d'un message flash
                        $this->addFlash('error', 'Cette date de réunion (' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ') existe déjà pour un autre RDD de ce signal. Veuillez en choisir une autre date.');
                        return $this->render('gestion_releves_de_decision/RDD_modif.html.twig', [
                            'signalId' => $signalId,
                            'autresRDDs' => $autresRDDs,
                            'form' => $form->createView(),
                        ]);
                    } else {

                        $em->persist($RDD);
                        // $em->persist($signal);
                        $em->flush();

                        $this->addFlash('success', 'Le relevé de décision pour la date ' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ' a bien été modifié');
                    }

                    return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);

                } else {

                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif RDD - 03 - formulaire invalide');
                        $this->logger->info('modif RDD - 03 - formulaire invalide');
                    }
                }
            }
            if ($form->get('ajout_mesure')->isClicked()) {
                // Ajout d'une mesure

                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('modif RDD - 04 - bouton ajout d\'une mesure');
                    $this->logger->info('modif RDD - 04 - bouton ajout d\'une mesure');
                }
                return $this->redirectToRoute('app_creation_mesure', ['signalId' => $signalId, 'rddId' => $rddId]);
            }
        }






        return $this->render('gestion_releves_de_decision/RDD_modif.html.twig', [
            'signalId' => $signalId,
            'signal' => $signal,
            'autresRDDs' => $autresRDDs,
            // 'date_reunion' => $date_reunion,
            // 'rdd' => $RDD,
            'form' => $form->createView(),
            'TypeModifCreation' => 'modification',
            'lstMesures' => $lstMesures,
            'isLatestRdd' => $isLatestRdd,
            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
        ]);
    }
}
