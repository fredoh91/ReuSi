<?php

namespace App\Form;

use App\Entity\Suivi;
use App\Entity\ReunionSignal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SuiviInitialDetailType extends SuiviType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        
        // Masquer le champ DescriptionSuivi pour le suivi initial
        // car il est hérité du SuiviType parent et n'est pas souhaité ici.
        // On le supprime de ce formulaire spécifique.
        $builder->remove('DescriptionSuivi');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        
        // Options par défaut pour SuiviInitialDetailType
        $resolver->setDefaults([
            'required_fields' => [
                'DescriptionSuivi' => false,
                'PiloteDS' => false,
                'reunionSignal' => false,
                'EmetteurSuivi' => false,
            ],
            'disabled_fields' => [
                'DescriptionSuivi' => false,
                'PiloteDS' => false,
                'reunionSignal' => false,
                'EmetteurSuivi' => false,
            ],
        ]);
    }
    
    public function getParent(): string
    {
        return SuiviType::class;
    }
}
