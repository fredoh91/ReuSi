<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RechLaboType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dci', TextType::class, [
                'label' => 'DCI',
            ])
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination',
            ])
            ->add(
                'recherche',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary btn-sm m-1'],
                    'label' => 'Rechercher',
                    'row_attr' => ['id' => 'recherche'],
                ]
            )
        
            ->add(
                'reset',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary btn-sm m-1'],
                    'label' => 'Reset',
                    'row_attr' => ['id' => 'reset'],
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options ici
            'attr' => [
                'data-turbo' => 'false',
                'class' => 'needs-validation',
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
