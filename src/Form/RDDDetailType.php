<?php

namespace App\Form;

use App\Entity\Signal;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RDDDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NumeroRDD', TextType::class, [
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('DescriptionRDD', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            ->add('reunionSignal', EntityType::class, [
                'class' => ReunionSignal::class,
                'choices' => $options['reunions'] ?? [],
                'choice_label' => function ($reunion) {
                    return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
                },
                'placeholder' => '-- Sélectionner une réunion --',
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            ->add('validation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success px-4'],
                'label' => 'Validation',
                'row_attr' => ['id' => 'validation'],
            ])
            ->add('annulation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary px-4'],
                'label' => 'Annulation',
                'row_attr' => ['id' => 'annulation'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReleveDeDecision::class,
            'reunions' => [],
        ]);
    }
}
