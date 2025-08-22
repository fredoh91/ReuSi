<?php

// src\Controller\SecurityController.php

namespace App\Controller;

use App\Form\TogglePasswordForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

    // DEBUG pour voir le token
    if ($request->isMethod('POST')) {
        dump('Token reçu:', $request->request->get('_token'));
        dump('Token généré:', $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue());
    }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // $form = $this->createForm(TogglePasswordForm::class);



        $form = $this->createForm(TogglePasswordForm::class, null, [
            'last_username' => $lastUsername
        ]);


        // return $this->render('security/login.html.twig', [
        //     'last_username' => $lastUsername,
        //     'error' => $error,
        // ]);

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'form' => $form->createView()
        ]);

        // return $this->render('security/login.html.twig', [
        //     'last_username' => $lastUsername,
        //     'error' => $error,
        // ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
