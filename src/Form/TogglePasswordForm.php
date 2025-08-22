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