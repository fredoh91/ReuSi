<?php

namespace App\Controller;

use App\Entity\Signal;
use App\Entity\ReunionSignal;
use App\Form\SignalDetailType;
use App\Entity\ReleveDeDecision;
use App\Form\SignalRDDDetailType;
// use App\Form\SignalDetailBtnProduitType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Model\SignalReleveReunionDTO;
use App\Form\SignalDetailBtnProduitRDDType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SignalDetailController extends AbstractController
{
    // #[Route('/signal_detail', name: 'app_signal_detail')]
    // public function index(): Response
    // {
    //     $form = $this->createForm(SignalDetailType::class);

    //     return $this->render('signal/signal_detail.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }


    #[Route('/signal_new', name: 'app_signal_new')]
    public function signal_new(
        EntityManagerInterface $em,
        // Request $request,
    ): Response
    {

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $signal = new Signal();
        $signal->setCreatedAt(new \DateTimeImmutable());
        $signal->setUpdatedAt(new \DateTimeImmutable());
        $signal->setUserCreate($userName);
        $signal->setUserModif($userName);

        $em->persist($signal);
        $em->flush();
        return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);

    }
    
    #[Route('/signal_modif/{signalId}', name: 'app_signal_modif', requirements: ['signalId' => '\d+'])]
    public function signal_modif(
        EntityManagerInterface $em,
        Request $request,
        int $signalId
    ): Response
    {
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

        // $signal = new Signal();
        // $signal->setCreatedAt(new \DateTimeImmutable());
        $signal->setUpdatedAt(new \DateTimeImmutable());
        // $signal->setUserCreate($userName);
        $signal->setUserModif($userName);

        $lstProduits = $signal->getProduits();

        $lstRDD = $signal->getReleveDeDecision();

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelled(100);

        // dd($date_reunion);

        $form = $this->createForm(SignalDetailBtnProduitRDDType::class, $signal, [
            // 'date_reunion' => $date_reunion,
        ]);



        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                // Annulation
                
                dump('01 - bouton annulation');

                return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
            }
            if ($form->get('validation')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de la validation
                    dump('02 - bouton validation');

                    $em->persist($signal);
                    $em->flush();

                    $this->addFlash('success', 'Le signal a bien été modifié');


                    return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);

                } else {
                    // Formulaire invalide
                    dump('03 - formulaire invalide');
                }
            }

            if ($form->get('ajout_produit')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de produit
                    dump('04 - bouton ajout produit');
                    // On persist et flush pour créer le signal "brouillon" et obtenir un ID
                    $em->persist($signal);
                    $em->flush();
        
                    // Redirection vers la route pour creations des produits
                    return $this->redirectToRoute('app_creation_produits', ['signalId' => $signal->getId()]);                    
                } else {
                    // Formulaire invalide
                    dump('05 - formulaire invalide');
                }
            }

            if ($form->get('ajout_produit_saisie_manu')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de produit
                    dump('06 - bouton ajout produit manuellement');
                    // On persist et flush pour créer le signal "brouillon" et obtenir un ID
                    $em->persist($signal);
                    $em->flush();
        
                    // Redirection vers la route pour creations des produits dans un formulaire vide
                    return $this->redirectToRoute('app_ajout_produit', ['signalId' => $signal->getId(), 'codeCIS' => null]);                  
                } else {
                    // Formulaire invalide
                    dump('07 - formulaire invalide');
                }
            }

            if ($form->get('ajout_RDD')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de RDD
                    dump('08 - bouton ajout RDD');
                    // On persist et flush pour créer le signal "brouillon" et obtenir un ID
                    $em->persist($signal);
                    $em->flush();
        
                    // Redirection vers la route pour creations des produits
                    return $this->redirectToRoute('app_creation_RDD', ['signalId' => $signal->getId()]);                    
                } else {
                    // Formulaire invalide
                    dump('09 - formulaire invalide');
                }
            }
        }
        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'lstProduits' => $lstProduits,
            'lstRDD' => $lstRDD,
        ]);
    }

    #[Route('/signal_detail/{signalId}', name: 'app_signal_detail', requirements: ['signalId' => '\d+'])]
    public function signal_detail(
        EntityManagerInterface $em,
        Request $request,
        int $signalId
    ): Response
    {
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
