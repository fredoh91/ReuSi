<?php

namespace App\Form;

use App\Entity\User;
// use App\Form\UserBaseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserEditMyPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        parent::buildForm($builder, $options);

        $builder

            ->add('email', EmailType::class, [
                'label' => 'E-mail : ',
                'attr' => ['readonly' => true],
                'required' => false,
                'disabled' => true,
            ])

            ->add('Prenom', TextType::class, [
                'label' => 'Prénom : ',
                'attr' => ['readonly' => true],
                'required' => false,
                'disabled' => true,
            ])

            ->add('Nom', TextType::class, [
                'label' => 'Nom : ',
                'attr' => ['readonly' => true],
                'required' => false,
                'disabled' => true,
            ])
            ->add('UserName', TextType::class, [
                'label' => 'Username : ',
                'attr' => ['readonly' => true],
                'required' => false,
                'disabled' => true,
            ])
            // ->add('roles', ChoiceType::class, [
            //     'choices' => [
            //         'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
            //         'ROLE_ADMIN' => 'ROLE_ADMIN',
            //         'ROLE_DMFR_ADMIN' => 'ROLE_DMFR_ADMIN',
            //         'ROLE_DMFR_REF' => 'ROLE_DMFR_REF',
            //         'ROLE_DMFR_GEST' => 'ROLE_DMFR_GEST',
            //         'ROLE_SURV_ADMIN' => 'ROLE_SURV_ADMIN',
            //         'ROLE_SURV_PILOTEVEC' => 'ROLE_SURV_PILOTEVEC',
            //         'ROLE_DMM_EVAL' => 'ROLE_DMM_EVAL',
            //         // 'ROLE_USER' => 'ROLE_USER',
            //     ],
            //     'attr' => ['readonly' => true],
            //     'required' => false,
            //     'disabled' => true,
            //     'expanded' => true,
            //     'multiple' => true,
            //     'label' => 'Rôle(s)'
            // ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Nouveau mot de passe :',
                    'attr' => [
                        'data-password-match-target' => 'first',
                        'data-action' => 'input->password-match#checkMatch',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe :',
                    'attr' => [
                        'data-password-match-target' => 'second',
                        'data-action' => 'input->password-match#checkMatch',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add(
                'Valider',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary m-2'],
                    'label' => 'Valider'
                ]
            )
            ->add(
                'Annuler',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary m-2'],
                    'label' => 'Annuler'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
