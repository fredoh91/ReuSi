<?php

namespace App\Form;

use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SignalDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre')
            ->add('DescriptionSignal')
            ->add('Indication')
            // ->add('DateCreation', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('Contexte')
            ->add('NiveauRisqueInitial', ChoiceType::class, [
                'choices' => [
                    'SHR' => 'SHR',
                    'SRI' => 'SRI',
                    'SRM' => 'SRM',
                    'SRF' => 'SRF',
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'readonly' => true,
                    'disabled' => false,
                ],
                'label' => 'Niveau de risque initial',
            ])
            ->add('NiveauRisqueFinal', ChoiceType::class, [
                'choices' => [
                    'SHR' => 'SHR',
                    'SRI' => 'SRI',
                    'SRM' => 'SRM',
                    'SRF' => 'SRF',
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'readonly' => true,
                    'disabled' => false,
                ],
                'label' => 'Niveau de risque final',
            ])
            ->add('AnaRisqueComment')
            // ->add('SourceSignal')
            ->add('SourceSignal', TextType::class, [
                'required' => false,
                'label' => 'Source du signal',
                'attr' => [
                    // On lie notre futur contrôleur Stimulus
                    'data-controller' => 'autocomplete',
                ],
            ])            
            ->add('RefSignal')
            ->add('IdentifiantSource')
            // ->add('UserCreate')
            // ->add('UserModif')
            // ->add('CreatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UpdatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('eval', SubmitType::class, [
            //     'attr' => ['class' => 'btn btn-primary m-2'],
            //     'label' => 'Validation',
            //     'row_attr' => ['id' => 'recherche'],
            // ])
            // ->add('reset', SubmitType::class, [
            //     'attr' => ['class' => 'btn btn-primary m-2'],
            //     'label' => 'Annulation',
            //     'row_attr' => ['id' => 'reset'],
            // ])
            ->add('validation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Validation',
                'row_attr' => ['id' => 'validation'],
            ])
            ->add('annulation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Annulation',
                'row_attr' => ['id' => 'annulation'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
        ]);
    }
}
