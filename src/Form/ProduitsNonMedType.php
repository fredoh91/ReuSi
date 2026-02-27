<?php

namespace App\Form;

// use App\Entity\Produits;
use App\Form\ProduitsBaseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitsNonMedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('typeSubstance', HiddenType::class, [ // Champ commun, masqué mais présent
            //     'data' => 'non-med',
            // ])
            ->add('productFamily', TextType::class, [
                'label' => 'Famille de produit',
                'required' => false,
            ])
            ->add('topProductName', TextType::class, [
                'label' => 'Type libellé (SYnonym ou Prefered Term)',
                'required' => false,
            ])
            ->add('unii_id', TextType::class, [
                'label' => 'UNII ID',
                'required' => false,
            ])
            ->add('cas_id', TextType::class, [
                'label' => 'CAS ID',
                'required' => false,
            ]);
    }

    public function getParent(): string
    {
        // On hérite des boutons et de la configuration de ProduitsType
        return ProduitsBaseType::class;
    }

}
