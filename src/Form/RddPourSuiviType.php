<?php

namespace App\Form;

use App\Entity\ReleveDeDecision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RddPourSuiviType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DescriptionRDD', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
                'label' => 'Description du relevé de décision',
            ])
            ->add('PassageCTP', ChoiceType::class, [
                'choices' => [
                    'Oui' => 'oui',
                    'Non' => 'non',
                ],
                'label' => 'Passage en CTP',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'attr' => ['class' => 'form-select'],
                'placeholder' => '',
                'required' => false,
            ])
            ->add('PassageRSS', ChoiceType::class, [
                'choices' => [
                    'Oui' => 'oui',
                    'Non' => 'non',
                ],
                'label' => 'Passage en RSS',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'attr' => ['class' => 'form-select'],
                'placeholder' => '',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ReleveDeDecision::class]);
    }
}