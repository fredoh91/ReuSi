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

        if ($form->isSubmitted() && $form->isValid()) {
            // Bouton "reset" : redirige vers la même page sans paramètres pour vider le formulaire
            if ($form->get('reset')->isClicked()) {
                return $this->redirectToRoute($request->attributes->get('_route'));
            }

            // Bouton "annulation" : redirige vers la page d'accueil (à adapter si besoin)
            // if ($form->get('annulation')->isClicked()) {
            //     return $this->redirectToRoute('app_home');
            // }

            // Si on arrive ici, c'est que "recherche" a été cliqué. On prend les données.
            $criteria = $form->getData();

        } else if ($request->query->count() > 0) {
            // Ce bloc gère le cas où la page est chargée avec des paramètres dans l'URL
            // (ex: un lien de pagination) sans que le formulaire ait été "soumis".
            // On remplit le formulaire avec les paramètres de l'URL pour qu'il reste affiché.
            $criteria = $request->query->all();
            $form->submit($criteria, false);
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

        if ($form->isSubmitted() && $form->isValid()) {


            // Gestion du bouton Réinitialiser (Reset)
            if ($form->get('reset')->isClicked()) {
                return $this->redirectToRoute('app_signal_fait_marquant_liste');
            }

            // La recherche ne s'exécute que si le bouton 'recherche' est cliqué 
            // ou si on a des paramètres de recherche dans l'URL (cas de la pagination)
            $queryParams = $request->query->all();
            // On exclut les paramètres techniques pour savoir si une recherche est active
            unset($queryParams['tab'], $queryParams['page']);

            if ($form->get('recherche')->isClicked() || !empty($queryParams)) {
                $criteria = $form->getData();
            }
        } elseif ($request->query->count() > 0) {
            // Gestion de la pagination (paramètres dans l'URL)
            $criteria = $request->query->all();
            // On soumet manuellement pour que le formulaire soit rempli
            $form->submit($criteria, false);
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

        return $this->render('signal_liste/signal_fait_marquant_liste.html.twig', [
            // 'searchForm' => $form->createView(),
            'pagination_signal' => $pagination_signal,
            'pagination_fait_marquant' => $pagination_fait_marquant,
            'form' => $form->createView(),
        ]);
    }    
}