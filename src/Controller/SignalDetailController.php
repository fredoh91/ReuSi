<?php

namespace App\Controller;

use App\Entity\Signal;
use Psr\Log\LoggerInterface;
use App\Entity\ReunionSignal;
use App\Form\SignalDetailType;
use App\Entity\ReleveDeDecision;
// use App\Form\SignalDetailBtnProduitType;
use App\Form\SignalRDDDetailType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Model\SignalReleveReunionDTO;
use App\Form\SignalDetailBtnProduitRDDType;
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

        $em->persist($signal);
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

        // $signal = new Signal();
        // $signal->setCreatedAt(new \DateTimeImmutable());
        $signal->setUpdatedAt(new \DateTimeImmutable());
        // $signal->setUserCreate($userName);
        $signal->setUserModif($userName);

        $lstProduits = $signal->getProduits();

        $lstRDD = $signal->getReleveDeDecision();

        // On récupère le RDD le plus récent pour le passer à la vue
        $latestRDD = $em->getRepository(ReleveDeDecision::class)->findLatestForSignal($signal);

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelled(100);

        $routeSource = $request->query->get('routeSource', null);
        // dd($date_reunion);
        $allowedRoutesSource = ['app_signal_liste', 'app_fait_marquant_liste'];


        $form = $this->createForm(SignalDetailBtnProduitRDDType::class, $signal, [
            // 'date_reunion' => $date_reunion,
        ]);



        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('annulation')->isClicked()) {
                // Annulation

                if ($this->kernel->getEnvironment() === 'dev') {
                    dump('modif signal - 01 - bouton annulation');
                    $this->logger->info('modif signal - 01 - bouton annulation');
                }

                // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                if ($routeSource && in_array($routeSource, $allowedRoutesSource)) {
                    return $this->redirectToRoute($routeSource, ['signalId' => $signal->getId()]);
                }
                return $this->redirectToRoute('app_signal_liste');
            }
            if ($form->get('validation')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de la validation

                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 02 - bouton validation');
                        $this->logger->info('modif signal - 02 - bouton validation');
                    }
                    $em->persist($signal);
                    $em->flush();

                    $this->addFlash('success', 'Le signal ' . $signalId . ' a bien été modifié');


                    // return $this->redirectToRoute('app_signal_modif', ['signalId' => $signal->getId()]);
                    if ($routeSource && in_array($routeSource, $allowedRoutesSource)) {
                        return $this->redirectToRoute($routeSource, ['signalId' => $signal->getId()]);
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

            if ($form->get('ajout_produit')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de produit
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 04 - bouton ajout produit');
                        $this->logger->info('modif signal - 04 - bouton ajout produit');
                    }
                    // On persist et flush pour créer le signal "brouillon" et obtenir un ID
                    $em->persist($signal);
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

            if ($form->get('ajout_produit_saisie_manu')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de produit
                    
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 06 - bouton ajout produit manuellement');
                        $this->logger->info('modif signal - 06 - bouton ajout produit manuellement');
                    }
                    // On persist et flush pour créer le signal "brouillon" et obtenir un ID
                    $em->persist($signal);
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

            if ($form->get('ajout_RDD')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de l'ajout de RDD
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 08 - bouton ajout RDD');
                        $this->logger->info('modif signal - 08 - bouton ajout RDD');
                    }
                    // On persist et flush pour créer le signal "brouillon" et obtenir un ID
                    $em->persist($signal);
                    $em->flush();

                    // Redirection vers la route pour creations des produits
                    return $this->redirectToRoute('app_creation_RDD', ['signalId' => $signal->getId()]);
                } else {
                    // Formulaire invalide
                    if ($this->kernel->getEnvironment() === 'dev') {
                        dump('modif signal - 09 - formulaire invalide');
                        $this->logger->info('modif signal - 09 - formulaire invalide');
                    }
                }
            }
        }
        return $this->render('signal/signal_modif.html.twig', [
            'form' => $form->createView(),
            'signal' => $signal,
            'lstProduits' => $lstProduits,
            'lstRDD' => $lstRDD,
            'latestRddId' => $latestRDD ? $latestRDD->getId() : null,
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
