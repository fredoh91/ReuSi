# Problème d'authentification Symfony avec UX Toggle Password

## Contexte
Application Symfony avec authentification par formulaire utilisant le composant `symfony/ux-toggle-password`. Le projet utilise Webpack Encore.

## Problème principal
Erreurs de dépréciation dans mon fichier de log, lié a UX-toggle-password :

[2025-08-22T10:29:13.014239+02:00] deprecation.INFO: User Deprecated: Since symfony/ux-toggle-password 2.29.0: The package is deprecated and will be removed in 3.0. Follow the migration steps in https://github.com/symfony/ux/tree/2.x/src/TogglePassword to keep using TogglePassword in your Symfony application. {"exception":"[object] (ErrorException(code: 0): User Deprecated: Since symfony/ux-toggle-password 2.29.0: The package is deprecated and will be removed in 3.0. Follow the migration steps in https://github.com/symfony/ux/tree/2.x/src/TogglePassword to keep using TogglePassword in your Symfony application. at D:\\www_htdocs\\ReuSi\\vendor\\symfony\\ux-toggle-password\\src\\TogglePasswordBundle.php:16)"} []


## Configuration actuelle

### SecurityController.php
```php
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

```

### TogglePasswordForm.php (version actuelle)
```php
<?php
// src/Form/TogglePasswordForm.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TogglePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // IMPORTANT: Le nom du champ doit être '_username' pour l'auth Symfony
            ->add('_username', EmailType::class, [
                'label' => 'Email',
                'data' => $options['last_username'], // Récupère la dernière saisie
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                    'required' => true,
                    'autofocus' => true,
                    'id' => 'inputEmail',
                ],
            ])
            // IMPORTANT: Le nom du champ doit être '_password' pour l'auth Symfony
            ->add('_password', PasswordType::class, [
                'label' => 'Mot de passe',
                'toggle' => true,
                'hidden_label' => 'Masquer',
                'visible_label' => 'Afficher',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                    'required' => true,
                    'id' => 'inputPassword',
                ]
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'Se souvenir de moi',
                'required' => false,
                'attr' => [
                    'class' => 'checkbox mb-3'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Connexion',
                'attr' => [
                    'class' => 'btn btn-lg btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'authenticate',
            'last_username' => '',
            // Important: pas de data_class pour un formulaire d'authentification
            'data_class' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        // Retourne une chaîne vide pour éviter le préfixe du formulaire
        // Permet d'avoir directement _username, _password au lieu de form[_username]
        return '';
    }
}
```

### Template login.html.twig
```twig
{# templates\security\login.html.twig #}

{% extends 'base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    {{ encore_entry_link_tags('app') }}
{% endblock %}

{% block title %}Connexion{% endblock %}

{% block body %}
<div class="container my-3">
    <div class="row">
        <div class="col">
        {# {% include 'security/connexionClassique.html.twig' %} #}
        {% include 'ux_packages/toggle_password.html.twig' with {'form': form} %}

            {# {% include 'ux_packages/toggle_password_pure_html.html.twig' %} #}
        </div>
    </div>
    
    {# <a class="" href="{{ asset('guide_utilisateur/guide_utilisateur.html') }}">Guide utilisateur</a> #}
</div>            
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {{ encore_entry_script_tags('app') }}
{% endblock %}


```

### Template toggle_password.html.twig
```twig
{# templates/ux_packages/toggle_password.html.twig #}

{% block body %}
    {% if form is defined %}
        {{ form_start(form, {
            'method': 'post',
            'action': path('app_login'),
            'attr': {
                'novalidate': 'novalidate'
            }
        }) }}
        
            {% if error %}
                <div class="alert alert-danger">
                    {{ error.messageKey|trans(error.messageData, 'security') }}
                </div>
            {% endif %}
            
            <h1 class="h3 mb-3 font-weight-normal">Merci de vous connecter</h1>
           
            {{ form_row(form._username, {
                'label': 'Email',
                'label_attr': {'for': 'inputEmail'}
            }) }}
           
            {{ form_row(form._password, {
                'label': 'Mot de passe',
                'label_attr': {'for': 'inputPassword'}
            }) }}
            
            {{ form_row(form._remember_me) }}
            
            {# Token CSRF manuel #}
            <input type="hidden" name="_token" value="{{ csrf_token('authenticate') }}">
            
            {{ form_widget(form.submit) }}
            
        {{ form_end(form, {'render_rest': false}) }}
    {% endif %}
{% endblock %}
```


## Questions pour Gemini
1. Peux tu aller voir la solution proposé sur l'URL :  https://github.com/symfony/ux/tree/2.x/src/TogglePassword
2. Faut-il utiliser `getBlockPrefix()` qui retourne '' ou accepter le préfixe automatique ?
3. Comment configurer correctement `username_parameter` et `password_parameter` dans security.yaml selon le choix du préfixe ?
4. Y a-t-il une meilleure approche pour intégrer `symfony/ux-toggle-password` avec l'authentification Symfony ?

## Contraintes
- Utilisation de Webpack Encore (pas AssetMapper)
- Composant `symfony/ux-toggle-password` déjà installé et fonctionnel
- L'application doit avoir la sécurité CSRF activée en production
- Toujours me répondre en français
- Propose moi le code, mais ne modifie pas toi même les fichiers

## Objectif
Avoir un formulaire de connexion avec toggle password qui fonctionne avec le CSRF activé.