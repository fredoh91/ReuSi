<?php

namespace App\Form;

use App\Entity\ReunionSignal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReunionSignalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DateReunion', DateType::class, [
                'label' => 'Date de la réunion',
                // 'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            // ->add('ReunionAnnulee', CheckboxType::class, [
            //     'label' => 'Marquer comme annulée',
            //     'required' => false,
            //     'attr' => ['class' => 'form-check-input'],
            // ])
            ->add('statutReunion', ChoiceType::class, [
                'label' => 'Statut de la réunion',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'Prévue' => 'prevue',
                    'Réalisée' => 'realisee',
                    'Annulée' => 'annulee',
                ],
            ])
            ->add('annulation', SubmitType::class, [
                'label' => $options['annulation_button_label'],
                'attr' => [
                    'class' => 'btn btn-secondary',
                    'formnovalidate' => 'formnovalidate' 
                ],
                'validation_groups' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['save_button_label'],
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReunionSignal::class,
            'save_button_label' => 'Enregistrer les modifications',
            'annulation_button_label' => 'Annulation / retour à la liste',
        ]);
    }
}
