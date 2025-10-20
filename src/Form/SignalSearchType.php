<?php

namespace App\Form;

use App\Entity\ReunionSignal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SignalSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre', TextType::class, [
                'required' => false,
                'label' => 'Titre du signal'
            ])
            ->add('Description', TextType::class, [
                'required' => false,
                'label' => 'Contenu (Signal, Suivi, RDD)'
            ])
            ->add('Indication', TextType::class, [
                'required' => false,
            ])
            ->add('Contexte', TextType::class, [
                'required' => false,
            ])
            ->add('dateReunionDebut', DateType::class, [
                'label' => 'Date réunion (début)',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateReunionFin', DateType::class, [
                'label' => 'Date réunion (fin)',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dci', TextType::class, [
                'label' => 'DCI',
                'required' => false,
            ])
            ->add('denomination', TextType::class, [
                'label' => 'Dénomination produit',
                'required' => false,
            ])
            ->add('recherche', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-primary border border-dark']
            ])
            ->add('reset', SubmitType::class, [
                'label' => 'Réinitialiser',
                'attr' => ['class' => 'btn btn-secondary border border-dark']
            ])
            ->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => ['class' => 'btn btn-light border border-dark']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}