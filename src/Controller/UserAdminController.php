<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[Security("is_granted('ROLE_SUPER_ADMIN')")]
#[IsGranted('ROLE_REUSI_SUPER_ADMIN')]
class UserAdminController extends AbstractController
{
    #[Route('/super_admin/liste_user', name: 'app_liste_user')]
    public function index(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {   
        $entityManager = $doctrine->getManager();
        $TousUsers = $entityManager->getRepository(User::class)->findAll();
        
        $NbUsers = count($TousUsers);
        // dump(count($TousSusars));
        $Users = $paginator->paginate(
            $TousUsers, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            100 // Nombre de résultats par page
        );
        
        return $this->render('user/liste_user.html.twig', [
            'Users' => $Users,
            // 'form' => $form->createView(),
            'NbUsers' => $NbUsers,
        ]);
    }
}
