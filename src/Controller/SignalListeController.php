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
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Bouton "reset" : redirige vers la même page sans paramètres pour vider le formulaire
            if ($form->get('reset')->isClicked()) {
                return $this->redirectToRoute($request->attributes->get('_route'));
            }

            // Bouton "annulation" : redirige vers la page d'accueil (à adapter si besoin)
            if ($form->get('annulation')->isClicked()) {
                return $this->redirectToRoute('app_home');
            }

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
}