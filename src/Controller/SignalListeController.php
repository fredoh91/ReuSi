<?php

namespace App\Controller;

use App\Entity\Signal;
use App\Form\SignalSearchType;
use App\Repository\SignalRepository;
use Knp\Component\Pager\PaginatorInterface;
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
        SignalRepository $signalRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {
        $criteria = [];
        // On passe null comme data initiale, et on configure la méthode GET
        $form = $this->createForm(SignalSearchType::class, null, [
            'method' => 'GET',
            'ModeForm' => 'rech_sig_FM',
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Bouton "reset" : redirige vers la même page sans paramètres pour vider le formulaire
            if ($form->isValid() && $form->get('reset')->isClicked()) {
                return $this->redirectToRoute($request->attributes->get('_route'));
            }

            // On prend les données même si le formulaire n'est pas "valide" au sens strict de Symfony
            $criteria = $form->getData();

        } else if ($request->query->count() > 0) {
            $queryParams = $request->query->all();
            $form->submit($queryParams, false);

            // On identifie s'il y a de réels critères de recherche
            $searchParams = $queryParams;
            unset($searchParams['tab'], $searchParams['page']);

            if (!empty($searchParams)) {
                $criteria = $form->getData();
            } else {
                $criteria = [];
            }
        }

        // On passe les critères (qu'ils viennent du form ou de l'URL) au repository
        $queryBuilder = $signalRepository->findByTypeSignalWithCriteria($typeSignal, $criteria);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('signal_liste/signal_liste.html.twig', [
            'searchForm' => $form->createView(),
            'pagination' => $pagination,
            'typeSignal' => $typeSignal,
        ]);
    }

    #[Route('/signal_fait_marquant_liste', name: 'app_signal_fait_marquant_liste')]
    public function listeTousSignaux(
        SignalRepository $signalRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {

    
        $form = $this->createForm(SignalSearchType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
            'ModeForm' => 'rech_sig_FM',
        ]);
        $form->handleRequest($request);
        
        $criteria = [];
if ($form->isSubmitted()) {
    // Gestion du bouton Réinitialiser (Reset)
    if ($form->isValid() && $form->get('reset')->isClicked()) {
        return $this->redirectToRoute('app_signal_fait_marquant_liste');
    }

    // On récupère les données du formulaire même si non "valide" (plus souple pour la recherche)
    $criteria = $form->getData();

    $queryParams = $request->query->all();
    unset($queryParams['tab'], $queryParams['page']);

    if ($form->isValid() && !$form->get('recherche')->isClicked() && empty($queryParams)) {
        $criteria = [];
    }
} elseif ($request->query->count() > 0) {
    // Gestion de la pagination (paramètres dans l'URL)
    $queryParams = $request->query->all();
    $form->submit($queryParams, false);

    // On n'applique les critères du formulaire que si de réels paramètres de recherche sont présents
    $searchParams = $queryParams;
    unset($searchParams['tab'], $searchParams['page']);

    if (!empty($searchParams)) {
        $criteria = $form->getData();
    } else {
        $criteria = [];
    }
}

        // On utilise findByTypeSignalWithCriteria qui gère maintenant le "non-cloture" par défaut
        $queryBuilder_signal = $signalRepository->findByTypeSignalWithCriteria('signal', $criteria);
        $queryBuilder_fait_marquant = $signalRepository->findByTypeSignalWithCriteria('fait_marquant', $criteria);

        $pagination_signal = $paginator->paginate(
            $queryBuilder_signal,
            $request->query->getInt('page', 1),
            10
        );
        $pagination_fait_marquant = $paginator->paginate(
            $queryBuilder_fait_marquant,
            $request->query->getInt('page', 1),
            10
        );

        $activeTab = $request->query->get('tab', 'signaux');

        return $this->render('signal_liste/signal_fait_marquant_liste.html.twig', [
            // 'searchForm' => $form->createView(),
            'pagination_signal' => $pagination_signal,
            'pagination_fait_marquant' => $pagination_fait_marquant,
            'form' => $form->createView(),
            'activeTab' => $activeTab,
        ]);
    }    
}