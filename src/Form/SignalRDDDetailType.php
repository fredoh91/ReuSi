<?php

namespace App\Form;

use App\Entity\Signal;
use App\Form\RDDDetailType;
use App\Entity\ReunionSignal;
use App\Form\SignalDetailType;
use App\Entity\ReleveDeDecision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SignalRDDDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('NumeroRDD')
            // ->add('DescriptionRDD')
            // ->add('UserCreate')
            // ->add('UserModif')
            // ->add('CreatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UpdatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('SignalLie', EntityType::class, [
            //     'class' => Signal::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('reunionSignal', EntityType::class, [
            //     'class' => ReunionSignal::class,
            //     'choice_label' => 'id',
            // ])
            ->add('signal', SignalDetailType::class, [
                'label' => false, // Pas de label pour le sous-formulaire
            ])
            ->add('releve', RDDDetailType::class, [
                'label' => false, // Pas de label pour le sous-formulaire
            ])
            ->add('reunionSignal', EntityType::class, [
                'class' => ReunionSignal::class,
                'choices' => $options['date_reunion'] ?? [],
                'choice_label' => function ($reunion) {
                    return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
                },
                'placeholder' => '-- Choisir une réunion --',
                'required' => false,
                'label' => 'Date de la réunion',
            ])
            ->add('validation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Validation',
                'row_attr' => ['id' => 'validation'],
            ])
            ->add('ajout_produit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Ajout produit(s)',
                'row_attr' => ['id' => 'ajout_produit'],
            ])
            ->add('reset', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Annulation',
                'row_attr' => ['id' => 'reset'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => ReleveDeDecision::class,
            'data_class' => \App\Form\Model\SignalReleveReunionDTO::class,
            'date_reunion' => [],
        ]);
    }
}
