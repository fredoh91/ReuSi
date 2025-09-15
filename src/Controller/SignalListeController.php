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
    #[Route('/signal_liste', name: 'app_signal_liste', defaults: ['typeSignal' => 'signal'])]
    #[Route('/fait_marquant_liste', name: 'app_fait_marquant_liste', defaults: ['typeSignal' => 'fait_marquant'])]
    public function liste(
        string $typeSignal,
        EntityManagerInterface $em
    ): Response
    {
        $signals = $em->getRepository(Signal::class)->findByTypeSignal($typeSignal);

        $template = match ($typeSignal) {
            'signal' => 'signal_liste/signal_liste.html.twig',
            'fait_marquant' => 'signal_liste/fait_marquant_liste.html.twig',
        };

        return $this->render($template, [
            'signals' => $signals,
        ]);
    }
}
