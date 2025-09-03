<?php

namespace App\Controller;

use App\Entity\ReunionSignal;
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
    public function index(
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

        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelled(100);
        
        return $this->render('gestion_releves_de_decision/index.html.twig', [
            'signalId' => $signalId,
            'date_reunion' => $date_reunion,
        ]);
    }
}
