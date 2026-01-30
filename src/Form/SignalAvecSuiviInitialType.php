<?php

namespace App\Form;

use App\Form\SignalDetailType;
use App\Form\SignalRddDetailType;
use App\Form\SuiviInitialDetailType;
use App\Form\Model\SignalAvecSuiviInitialDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalAvecSuiviInitialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('signal', SignalDetailType::class, [
                'label' => false,
                'add_produit_button' => true,
                'add_produit_saisie_manu_button' => true,
                'add_suivi_button' => true,
                'add_mesure_button' => true,
            ])
            ->add('suiviInitial', SuiviInitialDetailType::class, [
                'label' => 'Suivi Initial (N°0)',
                'reunions' => $options['reunions'],
                'required' => false,
            ])
            ->add('rddInitial', SignalRddDetailType::class, [
                'label' => 'Relevé de Décision Initial (N°0)', // Changement ici
                'is_initial' => true, // Option pour masquer certains champs si nécessaire
                'required' => false,
                // 'reunions' => $options['reunions'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SignalAvecSuiviInitialDTO::class,
            'reunions' => [],
        ]);
    }
}