<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Entity\StatutSignal;
use App\Form\SuiviDetailType;
use App\Entity\ReleveDeDecision;
use App\Repository\SignalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ReunionSignal; // Ajout du use
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class GestionSuivisController extends AbstractController
{
    #[Route('/signal/{signalId}/modif_suivi/{suiviId}', name: 'app_modif_suivi')]
    public function modif_suivi(
        int $signalId,
        int $suiviId,
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
        $suivi = $em->getRepository(Suivi::class)->find($suiviId);

        // Gérer le cas où le suivi n'existe pas
        if (!$suivi) {
            throw $this->createNotFoundException('Le suivi avec l\'id ' . $suiviId . ' n\'existe pas.');
        }

        return $this->render('gestion_suivis/suivi_modif.html.twig', [
            'signalId' => $signalId,
            'signal' => $signal,
            'suivi' => $suivi,
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

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 100, 'DESC');

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

        // Création et gestion du formulaire
        $form = $this->createForm(SuiviDetailType::class, $suivi, [
            'reunions' => $date_reunion,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
            }

            if ($form->get('validation')->isClicked()) {
                // Validation spécifique pour la date de réunion
                $reunionSelectionnee = $form->get('reunionSignal')->getData();
                if (!$reunionSelectionnee) {
                    $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Ce champ est obligatoire.'));
                    $this->addFlash('error', 'Veuillez sélectionner une date de réunion avant de valider le formulaire.');
                }

                if ($form->isValid()) {
                    // On vérifie que la réunion n'est pas déjà utilisée par un autre suivi/RDD de ce signal
                    $rddExistante = $em->getRepository(ReleveDeDecision::class)->findOneBySignalAndReunionExcludingRdd($signal, $reunionSelectionnee, null);
                    if ($rddExistante) {
                        $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Cette réunion est déjà liée à un autre RDD/Suivi de ce signal.'));
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
            'typeModifCreation' => 'creation',
            'lstMesures' => $nouvellesMesures, // On peut les passer à la vue pour un aperçu
            'isLatest' => true, // Un Suivi en création est considéré comme le plus récent pour l'UI
            'latestSuiviId' => $latestSuivi ? $latestSuivi->getId() : null,
        ]);


    }
}
