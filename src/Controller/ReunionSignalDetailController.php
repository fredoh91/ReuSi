<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Entity\Signal;
use App\Entity\ReunionSignal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReunionSignalDetailController extends AbstractController
{
    #[Route('/reunion_signal_detail/{reuSiId}/{tab}', 
        name: 'app_reunion_signal_detail', 
        requirements: ['reuSiId' => '\d+', 'tab' => 'nouveaux-signaux|suivis-signaux|nouveaux-fm|suivis-fm']
    )]
    public function reuSi_detail(
        EntityManagerInterface $em,
        Request $request,
        int $reuSiId,
        string $tab
    ): Response
    {        
        $reunionSignal = $em->getRepository(ReunionSignal::class)->find($reuSiId);
 
        if (!$reunionSignal) {
            throw $this->createNotFoundException('La réunion avec l\'id ' . $reuSiId . ' n\'existe pas.');
        }
 
        // Récupérer tous les suivis associés à cette réunion
        $suivis = $em->getRepository(Suivi::class)->findBy(
            ['reunionSignal' => $reunionSignal],
            ['id' => 'ASC'] // Tri par ID ou un autre critère pertinent
        );

        // Préparation des listes pour les 4 catégories
        $nouveauxSignaux = [];
        $suivisSignaux = [];
        $nouveauxFaitsMarquants = [];
        $suivisFaitsMarquants = [];

        foreach ($suivis as $suivi) {
            $signal = $suivi->getSignalLie();
            if (!$signal) continue;

            if ($suivi->getNumeroSuivi() === 0) { // C'est un "Nouveau"
                if ($signal->getTypeSignal() === 'fait_marquant') {
                    $nouveauxFaitsMarquants[] = $suivi;
                } else {
                    $nouveauxSignaux[] = $suivi;
                }
            } else { // C'est un "Suivi de"
                if ($signal->getTypeSignal() === 'fait_marquant') {
                    $suivisFaitsMarquants[] = $suivi;
                } else {
                    $suivisSignaux[] = $suivi;
                }
            }
        }


        // On cherche les signaux et fait_marquant :
        //      - la date de suivi initial est strictement inférieure a la date de la réunion en cours
        //      ET aucun suivi n'est lié a la réunion en cours
        //      ET non-cloturé
        $signauxAnterieurs = $em->getRepository(Signal::class)->findSignauxAnterieursNonClotures('signal', $reunionSignal);
        $faitsMarquantsAnterieurs = $em->getRepository(Signal::class)->findSignauxAnterieursNonClotures('fait_marquant', $reunionSignal);

        return $this->render('reunion_signal_detail/reunion_signal_detail.html.twig', [
            'currentTab' => $tab,
            'reunionSignal' => $reunionSignal,
            'nouveauxSignaux' => $nouveauxSignaux,
            'suivisSignaux' => $suivisSignaux,
            'nouveauxFaitsMarquants' => $nouveauxFaitsMarquants,
            'suivisFaitsMarquants' => $suivisFaitsMarquants,
            'signauxAnterieurs' => $signauxAnterieurs,
            'faitsMarquantsAnterieurs' => $faitsMarquantsAnterieurs,
        ]);
    }
}
