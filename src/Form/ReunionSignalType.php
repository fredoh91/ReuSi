<?php

namespace App\Form;

use App\Entity\ReunionSignal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReunionSignalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DateReunion', DateType::class, [
                'label' => 'Date de la réunion',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('ReunionAnnulee', CheckboxType::class, [
                'label' => 'Marquer comme annulée',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['save_button_label'],
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReunionSignal::class,
            'save_button_label' => 'Enregistrer les modifications',
        ]);
    }
}
