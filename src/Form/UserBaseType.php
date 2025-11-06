<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir une adresse email'
                    ])
                ],
                'attr' => ['readonly' => false],
                'required' => true,
            ])

            ->add('Prenom', TextType ::class, [
                'label' => 'Prénom : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un prénom'
                    ])
                ],
                'attr' => ['readonly' => false],
                'required' => true,
            ])

            ->add('Nom', TextType ::class, [
                'label' => 'Nom : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un nom'
                    ])
                ],
                'attr' => ['readonly' => false],
                'required' => true,
            ])
            ->add('UserName', TextType ::class, [
                'label' => 'Username : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un username'
                    ])
                ],
                'attr' => ['readonly' => false],
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_REUSI_SUPER_ADMIN' => 'ROLE_REUSI_SUPER_ADMIN',
                    'ROLE_REUSI_ADMIN' => 'ROLE_REUSI_ADMIN',
                    'ROLE_REUSI_SURV_ADMIN' => 'ROLE_REUSI_SURV_ADMIN',
                    'ROLE_REUSI_DMM_EVAL' => 'ROLE_REUSI_DMM_EVAL',
                    'ROLE_REUSI_SURV_EVAL' => 'ROLE_REUSI_SURV_EVAL',
                    'ROLE_REUSI_EDITOR' => 'ROLE_REUSI_EDITOR',
                    'ROLE_REUSI_USER' => 'ROLE_REUSI_USER',
                    // 'ROLE_USER' => 'ROLE_USER',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôle(s)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
    public function getBlockPrefix(): string
{
    return 'intervenant_substance_dmm_detail';
}
}
