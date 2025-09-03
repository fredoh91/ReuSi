<?php

namespace App\Controller;

use App\Entity\ReunionSignal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReunionSignalListeController extends AbstractController
{
    #[Route('/reunion_signal_liste', name: 'app_reunion_signal_liste')]
    public function reunion_signal_liste(
        EntityManagerInterface $em,
        Request $request): Response
    {
        $reunionsSignal = $em->getRepository(ReunionSignal::class)->findBy([], ['DateReunion' => 'DESC']);

        
        return $this->render('reunion_signal_liste/reunion_signal_liste.html.twig', [
            'reunionsSignal' => $reunionsSignal,
        ]);
    }
}
