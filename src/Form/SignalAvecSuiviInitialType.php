<?php

namespace App\Form;

use App\Form\Model\SignalAvecSuiviInitialDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalAvecSuiviInitialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On imbrique le formulaire du Signal, en utilisant le type passé en option
        $builder->add('signal', $options['signal_form_type'], [
            'label' => false,
        ]);

        // On imbrique le formulaire pour le Suivi initial
        $builder->add('suiviInitial', SuiviInitialType::class, [
            'label' => 'Suivi Initial',
            'reunions' => $options['reunions'],
        ]);

        // On imbrique le formulaire pour le RDD initial
        $builder->add('rddInitial', ReleveDeDecisionInitialType::class, [
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SignalAvecSuiviInitialDTO::class,
            // On définit une option pour pouvoir choisir le type de formulaire Signal à utiliser
            // Par défaut, on met le type de base.
            'signal_form_type' => SignalDetailType::class,
            'reunions' => [],
        ]);

        // On s'assure que l'option 'signal_form_type' est bien une classe de formulaire
        $resolver->setAllowedTypes('signal_form_type', 'string');
        $resolver->setAllowedTypes('reunions', 'array');
    }
}
