<?php

namespace App\Controller;

use App\Entity\ReunionSignal;
use App\Form\RechReunionSignalType;
use App\Form\ReunionSignalType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReunionSignalDateController extends AbstractController
{
    #[Route('/reunion_signal_date', name: 'app_reunion_signal_date', methods: ['GET'])]
    public function reunion_signal_date(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
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

        return $this->render('reunion_signal_date/reunion_signal_date.html.twig', [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination
        ]);
    }

    #[Route('/reunion_signal_date/new', name: 'app_reunion_signal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {


        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $reunionSignal = new ReunionSignal($userName);
        $reunionSignal->setStatutReunion('prevue');
        $form = $this->createForm(ReunionSignalType::class, $reunionSignal, [
            'save_button_label' => 'Ajouter la réunion'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->get('annulation')->isClicked()) {
            $routeSource = $request->query->get('routeSource', 'app_reunion_signal_date');
            return $this->redirectToRoute($routeSource, [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {


            $autreDate = $entityManager->getRepository(ReunionSignal::class)->findOneBy(['DateReunion' => $reunionSignal->getDateReunion()]);
            if ($autreDate) {
                $this->addFlash('error', 'Une réunion est déjà prévue pour le ' . $autreDate->getDateReunion()->format('d/m/Y') . '. Veuillez choisir une autre date.');
                return $this->render('reunion_signal_date/edit.html.twig', [
                    'reunion_signal' => $reunionSignal,
                    'form' => $form->createView(),
                ]);
            }


            $entityManager->persist($reunionSignal);
            $entityManager->flush();

            $this->addFlash('success', 'La nouvelle réunion a bien été ajoutée.');
            
            $routeSource = $request->query->get('routeSource', 'app_reunion_signal_date');

            return $this->redirectToRoute($routeSource, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reunion_signal_date/edit.html.twig', [
            'reunion_signal' => $reunionSignal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reunion_signal_date/{id}/edit', name: 'app_reunion_signal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReunionSignal $reunionSignal, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if ($user) {
            $userName = $user->getUserName(); // Appelle la méthode getUserName() de l'entité User
            // dd($userName); // Affiche le userName pour vérifier
        } else {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $form = $this->createForm(ReunionSignalType::class, $reunionSignal, [
            'save_button_label' => 'Modifier la réunion'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $routeSource = $request->query->get('routeSource', 'app_reunion_signal_date');

            
            
            if ($form->get('annulation')->isClicked()) {
                // return $this->redirectToRoute('app_reunion_signal_date', [], Response::HTTP_SEE_OTHER);
                return $this->redirectToRoute($routeSource, [], Response::HTTP_SEE_OTHER);
            }

            if ($form->get('save')->isClicked()) {

                $reunionSignal->setUpdatedAt(new \DateTimeImmutable());
                $reunionSignal->setUserModif($userName);
            }
            $entityManager->flush();

            $this->addFlash('success', 'La réunion a bien été modifiée.');

            // return $this->redirectToRoute('app_reunion_signal_date', [], Response::HTTP_SEE_OTHER);
            return $this->redirectToRoute($routeSource, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reunion_signal_date/edit.html.twig', [
            'reunion_signal' => $reunionSignal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reunion_signal_date/{id}', name: 'app_reunion_signal_delete', methods: ['POST'])]
    public function delete(Request $request, ReunionSignal $reunionSignal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reunionSignal->getId(), $request->request->get('_token'))) {
            // Sécurité : Vérifier si des suivis sont liés avant de supprimer
            if ($reunionSignal->getSuivis()->isEmpty()) {
                $entityManager->remove($reunionSignal);
                $entityManager->flush();
                $this->addFlash('success', 'La réunion a bien été supprimée.');
            } else {
                $this->addFlash('error', 'Impossible de supprimer cette réunion car des suivis y sont associés.');
            }
        }

        return $this->redirectToRoute('app_reunion_signal_date', [], Response::HTTP_SEE_OTHER);
    }
}