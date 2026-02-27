<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitsBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ce formulaire de base ne contient plus que les éléments communs à tous ses enfants
        $builder
            ->add('typeSubstance', HiddenType::class)
            ->add('Denomination', TextType::class, [
                'label' => 'Dénomination',
                'required' => false,
            ])
            ->add('DCI', TextType::class, [
                'label' => 'DCI',
                'required' => false,
            ])
            ->add(
                'validation', SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-primary m-1'
                    ],
                    'label' => 'Validation',
                ]
            )
            ->add(
                'annulation', SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-primary m-1',
                        'formnovalidate' => true,
                    ],
                    'label' => 'Annuler',
                    'row_attr' => ['id' => 'annulation'],
                ]
            )
        ;

        // AJOUT : Ajoute le bouton "Supprimer" si l'option show_delete_button est vraie
        if ($options['show_delete_button']) {
            $builder->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => ['class' => 'btn btn-danger m-1'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
            'show_delete_button' => false, // Option par défaut : ne pas afficher le bouton de suppression
        ]);
        // S'assurer que l'option show_delete_button est toujours un booléen
        $resolver->setAllowedTypes('show_delete_button', 'bool');
    }
}