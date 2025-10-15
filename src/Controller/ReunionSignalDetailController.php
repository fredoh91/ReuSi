<?php

namespace App\Controller;

use App\Entity\ReunionSignal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReunionSignalDetailController extends AbstractController
{
    #[Route('/reunion_signal_detail/{reuSiId}', name: 'app_reunion_signal_detail', requirements: ['reuSiId' => '\d+'])]
    public function reuSi_detail(
        EntityManagerInterface $em,
        Request $request,
        int $reuSiId
    ): Response
    {
        $reuSig = $em
            ->getRepository(ReunionSignal::class)
            ->find($reuSiId);

        return $this->render('reunion_signal_detail/reunion_signal_detail.html.twig', [
            'reuSig' => $reuSig,
        ]);
    }
}
