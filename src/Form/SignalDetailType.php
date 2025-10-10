<?php

namespace App\Form;

use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        
        // Ajout des champs spécifiques à SignalDetailType
        $builder
            ->add('AnaRisqueComment', TextareaType::class, [
                'label' => 'Commentaire de l\'analyse de risque',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
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
            ]);
        
        if ($options['add_produit_button']) {
            $builder->add('ajout_produit', SubmitType::class, [
                'label' => 'Rechercher un produit',
                'attr' => ['class' => 'btn btn-success m-2'],
            ]);
        }

        if ($options['add_produit_saisie_manu_button']) {
            $builder->add('ajout_produit_saisie_manu', SubmitType::class, [
                'label' => 'Saisir un produit manuellement',
                'attr' => ['class' => 'btn btn-info m-2'],
            ]);
        }

        if ($options['add_suivi_button']) {
            $builder->add('ajout_suivi', SubmitType::class, [
                'label' => 'Ajouter un suivi',
                'attr' => ['class' => 'btn btn-warning m-2'],
            ]);
        }

        if ($options['add_mesure_button']) {
            $builder->add('ajout_mesure', SubmitType::class, [
                'label' => 'Ajouter une mesure',
                'attr' => ['class' => 'btn btn-info m-2'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
            'required_fields' => [
                'Titre' => false,
                'DescriptionSignal' => false,
                'Indication' => false,
                'Contexte' => false,
                'SourceSignal' => false,
                'RefSignal' => false,
                'IdentifiantSource' => false,
                'NiveauRisqueInitial' => false,
                'NiveauRisqueFinal' => false,
                'directionPoleConcernes' => false,
            ],
            'disabled_fields' => [
                'Titre' => false,
                'DescriptionSignal' => false,
                'Indication' => false,
                'Contexte' => false,
                'SourceSignal' => false,
                'RefSignal' => false,
                'IdentifiantSource' => false,
                'NiveauRisqueInitial' => false,
                'NiveauRisqueFinal' => false,
                'directionPoleConcernes' => false,
            ],
            'readonly_fields' => [
                'NiveauRisqueInitial' => true,
                'NiveauRisqueFinal' => true,
            ],
            'add_produit_button' => true,
            'add_produit_saisie_manu_button' => true,
            'add_suivi_button' => true,
            'add_mesure_button' => true,
        ]);
    }
    
    public function getParent(): string
    {
        return SignalType::class;
    }
}