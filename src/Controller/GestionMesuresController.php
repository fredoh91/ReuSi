<?php

namespace App\Controller;

use App\Entity\MesuresRDD;
use Psr\Log\LoggerInterface;
use App\Form\MesureDetailType;
use App\Repository\SignalRepository;
use App\Repository\MesuresRDDRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReleveDeDecisionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
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

                return $this->redirectToRoute('app_modif_RDD', ['signalId' => $signal->getId(), 'rddId' => $rddId]);
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

                    $this->addFlash('success', 'La mesure a bien été créée.');

                    return $this->redirectToRoute('app_modif_RDD', ['signalId' => $signal->getId(), 'rddId' => $rddId]);

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

                return $this->redirectToRoute('app_modif_RDD', ['signalId' => $signal->getId(), 'rddId' => $rddId]);
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

                    $this->addFlash('success', 'La mesure a bien été modifiée.');

                    return $this->redirectToRoute('app_modif_RDD', ['signalId' => $signal->getId(), 'rddId' => $rddId]);

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
}
