<?php

namespace App\Controller;

use App\Entity\ReunionSignal;
use App\Form\RechReunionSignalType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReunionSignalListeController extends AbstractController
{
    #[Route('/reunion_signal_liste', name: 'app_reunion_signal_liste')]
    public function reunion_signal_liste(
        EntityManagerInterface $em,
        PaginatorInterface $paginator, 
        Request $request
    ): Response
    {
        $reunionRepo = $em->getRepository(ReunionSignal::class);

        $searchForm = $this->createForm(RechReunionSignalType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->get('reinitialiser')->isClicked()) {
            return $this->redirectToRoute('app_reunion_signal_liste');
        }

        $criteria = $searchForm->getData();
        $reunionsQuery = $reunionRepo->findByCriteriaWithDetails($criteria);

        $pagination = $paginator->paginate(
            $reunionsQuery, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('reunion_signal_liste/reunion_signal_liste.html.twig', [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination
        ]);
    }
}
