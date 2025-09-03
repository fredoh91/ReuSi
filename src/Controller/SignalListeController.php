<?php

namespace App\Controller;

use App\Entity\Signal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SignalListeController extends AbstractController
{
    #[Route('/signal_liste', name: 'app_signal_liste')]
    public function signal_liste(
        EntityManagerInterface $em,
        Request $request): Response
    {
        $signals = $em->getRepository(Signal::class)->findAll();

        return $this->render('signal_liste/signal_liste.html.twig', [
            'signals' => $signals,
        ]);
    }
}
