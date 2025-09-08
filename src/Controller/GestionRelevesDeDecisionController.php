<?php

namespace App\Controller;

use App\Form\RDDDetailType;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
use App\Repository\SignalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class GestionRelevesDeDecisionController extends AbstractController
{
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

        $RDD = new ReleveDeDecision();
        $RDD->setSignalLie($signal);
        $RDD->setNumeroRDD($nextNumeroRDD);

        $RDD->setCreatedAt(new \DateTimeImmutable());
        $RDD->setUpdatedAt(new \DateTimeImmutable());
        $RDD->setUserCreate($userName);
        $RDD->setUserModif($userName);

        // $em->persist($RDD);

        $form = $this->createForm(RDDDetailType::class, $RDD, [
            'reunions' => $date_reunion,
        ]);
        $form->handleRequest($request);
        // $em->flush();
        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                // Annulation
                
                dump('01 - bouton annulation');

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
                    dump('02 - bouton validation');

                    // avant de faire le persist, on vérifie que la date de la réunion selectionnée n'est pas déjà liée à un autre RDD de ce signal
                    $reunionSelectionnee = $form->get('reunionSignal')->getData();
                    $rddExistante = $em->getRepository(ReleveDeDecision::class)->findOneBy([
                        'SignalLie' => $signal,
                        'reunionSignal' => $reunionSelectionnee,
                    ]);
                    if ($rddExistante) {
                        $form->get('reunionSignal')->addError(new \Symfony\Component\Form\FormError('Cette réunion est déjà liée à un autre RDD de ce signal.'));
                        // affichage d'un message flash
                        $this->addFlash('error', 'Cette date de réunion (' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ') existe déjà pour un autre RDD de ce signal. Veuillez en choisir une autre date.');
                        return $this->render('gestion_releves_de_decision/RDD_detail.html.twig', [
                            'signalId' => $signalId,
                            'autresRDDs' => $autresRDDs,
                            'form' => $form->createView(),
                        ]);
                    } else {
                        $RDD->setReunionSignal($reunionSelectionnee);
                        $signal->addReunionSignal($reunionSelectionnee); // Met à jour la relation ManyToMany dans l'entité Signal
                        $em->persist($RDD);
                        $em->persist($signal);
                        $em->flush();

                        $this->addFlash('success', 'Le relevé de décision pour la date ' . $reunionSelectionnee->getDateReunion()->format('d/m/Y') . ' a bien été créé');
                    }

                    return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);

                } else {
                    // Formulaire invalide
                    dump('03 - formulaire invalide');
                }
            }
        }

        return $this->render('gestion_releves_de_decision/RDD_detail.html.twig', [
            'signalId' => $signalId,
            'autresRDDs' => $autresRDDs,
            'form' => $form->createView(),
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

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelledAndNotLinkedToSignal($signalId, 100);

        // On regarde les autres RDD de ce signal et on récupère le numéro max




        return $this->render('gestion_releves_de_decision/RDD.html.twig', [
            'signalId' => $signalId,
            'date_reunion' => $date_reunion,
        ]);
    }
}
