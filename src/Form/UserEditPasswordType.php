<?php

namespace App\Form;

use App\Entity\User;
use App\Form\UserBaseType;
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

class UserEditPasswordType extends AbstractType
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
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_DMFR_ADMIN' => 'ROLE_DMFR_ADMIN',
                    'ROLE_DMFR_REF' => 'ROLE_DMFR_REF',
                    'ROLE_DMFR_GEST' => 'ROLE_DMFR_GEST',
                    'ROLE_SURV_ADMIN' => 'ROLE_SURV_ADMIN',
                    'ROLE_SURV_PILOTEVEC' => 'ROLE_SURV_PILOTEVEC',
                    'ROLE_DMM_EVAL' => 'ROLE_DMM_EVAL',
                    // 'ROLE_USER' => 'ROLE_USER',
                ],
                'attr' => ['readonly' => true],
                'required' => false,
                'disabled' => true,
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôle(s)'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe : ',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
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
