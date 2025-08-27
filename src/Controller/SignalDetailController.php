<?php

namespace App\Controller;

use App\Entity\Signal;
use App\Entity\ReunionSignal;
use App\Form\SignalDetailType;
use App\Entity\ReleveDeDecision;
use App\Form\SignalRDDDetailType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Model\SignalReleveReunionDTO;
use Symfony\Component\HttpFoundation\Request;
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
    public function signal_new(EntityManagerInterface $em, Request $request): Response
    {

        $signal = new Signal();
        $RDD = new ReleveDeDecision();
        $RDD->setNumeroRDD('1');       //   C'est un nouveau signal, il s'agit donc du RDD n°1
        $RDD->setSignalLie($signal);
        $dto = new SignalReleveReunionDTO();
        $dto->signal = $signal;
        $dto->releve = $RDD;
        $date_reunion = $em->getRepository(ReunionSignal::class)->findReunionsNotCancelled(100);

        // dd($date_reunion);

        $form = $this->createForm(SignalRDDDetailType::class, $dto, [
            'date_reunion' => $date_reunion,
        ]);

        // $form = $this->createForm(SignalRDDDetailType::class, [
        //     'signal' => $signal,
        //     'releve' => $RDD,
        //     'date_reunion' => $date_reunion,
        // ]);

        // $form = $this->createForm(SignalRDDDetailType::class, [
        //     'data' => [
        //         'signal' => $signal,
        //         'releve' => $RDD,
        //     ]
        // ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('reset')->isClicked()) {
                // Annulation
                
                dump('01 - bouton annulation');

                return $this->redirectToRoute('app_home');
            }
            if ($form->get('validation')->isClicked()) {
                if ($form->isValid()) {
                    // Traitement de la validation
                    dump('02 - bouton validation');
                } else {
                    // Formulaire invalide
                    dump('03 - formulaire invalide');
                }
            }
        }
        return $this->render('signal/signal_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
