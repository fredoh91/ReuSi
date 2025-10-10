<?php

namespace App\Form;

use App\Form\ReleveDeDecisionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use App\Entity\ReleveDeDecision;
// use App\Entity\ReunionSignal;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\OptionsResolver\OptionsResolver;
// src/Form/ReleveDeDecisionModifType.php
class ReleveDeDecisionModifType extends AbstractType
{
    public function getParent()
    {
        return ReleveDeDecisionType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('validation', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => ['class' => 'btn btn-secondary'],
            ])
            ->add('ajout_mesure', SubmitType::class, [
                'label' => 'Ajouter une mesure',
                'attr' => ['class' => 'btn btn-secondary'],
            ])
            ;
    }
}