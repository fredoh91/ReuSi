<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SuiviDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('NumeroSuivi', TextType::class, [
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            // ->add('UserCreate')
            // ->add('UserModif')
            // ->add('CreatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UpdatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('validation', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => ['class' => 'btn btn-secondary'],
            ])
            ->add('ajout_mesure', SubmitType::class, [
                'label' => 'Ajouter une mesure',
                'attr' => ['class' => 'btn btn-info'],
            ]);
    }

    public function getParent(): string
    {
        return SuiviInitialType::class;
    }

    // La méthode configureOptions n'est plus nécessaire ici, 
    // car les options 'data_class' et 'reunions' sont déjà définies dans le parent.
    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     // ...
    // }
}
