<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// #[Security("is_granted('ROLE_DMFR_ADMIN') or is_granted('ROLE_SURV_ADMIN')")]
// #[IsGranted('ROLE_DMFR_ADMIN')]
// #[IsGranted('ROLE_SURV_ADMIN')]
#[IsGranted(new Expression('is_granted("ROLE_REUSI_ADMIN") or is_granted("ROLE_REUSI_SURV_ADMIN")'))]
class AdminTbRefController extends AbstractController
{
    #[Route('/admin_tbref', name: 'app_admin_tbref')]
    public function index(): Response
    {
        return $this->render('admin_tbref/liste_admin_tbref.html.twig', [
            // 'controller_name' => 'AdminTbRefController',
        ]);
    }
}
