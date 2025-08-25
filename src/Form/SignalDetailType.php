<?php

namespace App\Form;

use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('NiveauRisqueInitial')
            ->add('NiveauRisqueFinal')
            ->add('AnaRisqueComment')
            ->add('SourceSignal')
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
        ]);
    }
}
