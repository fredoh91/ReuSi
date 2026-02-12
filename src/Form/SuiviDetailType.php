<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SuiviDetailType extends SuiviType
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
                'disabled' => $options['disabled_fields']['NumeroSuivi'] ?? false,
            ])
            ->add('validation', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary m-2'],
            ])
            ->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'class' => 'btn btn-secondary m-2',
                    'formnovalidate' => 'formnovalidate',
                ],
            ])
            ->add('ajout_mesure', SubmitType::class, [
                'label' => 'Ajouter une mesure',
                'attr' => ['class' => 'btn btn-info m-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        
        // Options par défaut pour SuiviDetailType
        $resolver->setDefaults([
            'required_fields' => [
                'DescriptionSuivi' => true,
                'PiloteDS' => true,
                'reunionSignal' => false,
                'EmetteurSuivi' => true,
                'NumeroSuivi' => false,
            ],
            'disabled_fields' => [
                'DescriptionSuivi' => false,
                'PiloteDS' => false,
                'reunionSignal' => false,
                'EmetteurSuivi' => false,
                'NumeroSuivi' => true,
            ],
        ]);
    }
    
    public function getParent(): string
    {
        return SuiviType::class;
    }
}
