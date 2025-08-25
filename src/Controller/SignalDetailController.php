<?php

namespace App\Controller;

use App\Entity\Signal;
use App\Form\SignalDetailType;
use App\Entity\ReleveDeDecision;
use App\Form\SignalRDDDetailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SignalDetailController extends AbstractController
{
    #[Route('/signal_detail', name: 'app_signal_detail')]
    public function index(): Response
    {
        $form = $this->createForm(SignalDetailType::class);

        return $this->render('signal/signal_detail.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/signal_new', name: 'app_signal_new')]
    public function signal_new(EntityManagerInterface $em): Response
    {

        $signal = new Signal();
        $RDD = new ReleveDeDecision();
        $RDD->setNumeroRDD('1');       //   C'est un nouveau signal, il s'agit donc du RDD n°1
        $RDD->setSignalLie($signal);

        $form = $this->createForm(SignalRDDDetailType::class, [
            'signal' => $signal,
            'releve' => $RDD,
        ]);
        // $form = $this->createForm(SignalRDDDetailType::class, [
        //     'data' => [
        //         'signal' => $signal,
        //         'releve' => $RDD,
        //     ]
        // ]);

        return $this->render('signal/signal_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
