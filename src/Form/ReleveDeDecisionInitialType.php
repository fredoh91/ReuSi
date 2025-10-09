<?php

namespace App\Form;

use App\Entity\ReleveDeDecision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReleveDeDecisionInitialType extends AbstractType
{
    public function getParent(): string
    {
        return RddPourSuiviType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReleveDeDecision::class,
        ]);
    }
}