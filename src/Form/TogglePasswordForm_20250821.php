<?php

// src\Form\TogglePasswordForm.php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TogglePasswordForm_20250821 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     ->add('email', EmailType::class)
        //     ->add('password', PasswordType::class, [
        //         'toggle' => true,
        //     ])
        // ;
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                    'required' => true,
                    'autofocus' => true,
                    'id' => 'inputEmail',
                    'name' => 'email',
                ],
                // 'property_path' => 'email',
            ])
            // ->add('password', PasswordType::class, [
            //     'label' => 'Mot de passe',
            //     'attr' => [
            //         'class' => 'form-control',
            //         'autocomplete' => 'current-password',
            //         'required' => true,
            //         'toggle' => true
            //     ]
            // ])
            ->add('password', PasswordType::class, [
                'toggle' => true,
                'hidden_label' => 'Masquer',
                'visible_label' => 'Afficher',
                'property_path' => 'password',
                'attr' => [
                    'id' => 'inputPassword',
                    'name' => 'password',
                    ]
                ])
            // ->add('password', PasswordType::class, [
            //     'label' => 'Mot de passe',
            //     'attr' => [
            //         'class' => 'form-control',
            //         'autocomplete' => 'current-password',
            //         'required' => true
            //     ],
            //     'row_attr' => [
            //         'class' => 'position-relative' // Pour le positionnement du bouton
            //     ],
            //     'toggle' => true, // Ajoutez cette ligne
            //     'hidden_label' => 'Afficher/Masquer le mot de passe' // Optionnel
            // ])
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
            ])
            ->add('_csrf_token', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'value' => '{{ csrf_token("authenticate") }}'
                ],
            ]);        
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
            'last_username' => ''
        ]);
    }
}
