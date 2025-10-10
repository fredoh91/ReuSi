<?php

namespace App\Form;

use App\Form\Model\SuiviAvecRddDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviAvecRddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('suivi', SuiviDetailType::class, [
                'label' => false,
                'reunions' => $options['reunions'],
                'required_fields' => [
                    'DescriptionSuivi' => false,
                    'PiloteDS' => false,
                    'EmetteurSuivi' => false,
                    'reunionSignal' => false
                ]
            ])
            // ->add('rddLie', RddPourSuiviType::class, [
            ->add('rddLie', ReleveDeDecisionType::class, [
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SuiviAvecRddDTO::class]);
        $resolver->setRequired('reunions');
    }
}