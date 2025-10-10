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
        
        // Ajout des champs spécifiques à SuiviInitialDetailType si nécessaire
        // Par exemple, des boutons ou des champs supplémentaires
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
