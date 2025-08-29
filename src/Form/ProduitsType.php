<?php

namespace App\Form;

use App\Entity\Signal;
use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Denomination', TextType::class, [
                'label' => 'Dénomination',
            ])
            ->add('DCI', TextType::class, [
                'label' => 'DCI',
            ])
            ->add('Dosage', TextType::class, [
                'label' => 'Dosage',
            ])
            ->add('Voie', TextType::class, [
                'label' => 'Voie d\'administration',
            ])
            ->add('CodeATC', TextType::class, [
                'label' => 'Code ATC',
            ])
            ->add('LibATC', TextType::class, [
                'label' => 'Libellé ATC',
            ])
            ->add('TypeProcedure', TextType::class, [
                'label' => 'Type de procédure',
            ])
            ->add('CodeCIS', TextType::class, [
                'label' => 'Code CIS',
            ])
            // ->add('CodeVU', TextType::class, [
            //     'label' => 'Code VU',
            // ])
            ->add('CodeDossier', TextType::class, [
                'label' => 'Code Dossier',
            ])
            // ->add('NomVU', TextType::class, [
            //     'label' => 'Nom VU',
            // ])
            // ->add('Codex', TextType::class, [
            //     'label' => 'Codex',
            // ])
            ->add('Laboratoire', TextType::class, [
                'label' => 'Laboratoire',
            ])
            ->add('idLaboratoire', TextType::class, [
                'label' => 'ID Laboratoire',
            ])
            ->add('AdresseContact', TextType::class, [
                'label' => 'Adresse Contact',
            ])
            ->add('AdresseCompl', TextType::class, [
                'label' => 'Adresse Complémentaire',
            ])
            ->add('CodePost', TextType::class, [
                'label' => 'Code Postal',
            ])
            ->add('NomVille', TextType::class, [
                'label' => 'Nom de la Ville',
            ])
            ->add('TelContact', TextType::class, [
                'label' => 'Téléphone Contact',
            ])
            ->add('FaxContact', TextType::class, [
                'label' => 'Fax Contact',
            ])
            ->add('DboPaysLibAbr', TextType::class, [
                'label' => 'Pays',
            ])
            ->add('Titulaire', TextType::class, [
                'label' => 'Titulaire',
            ])
            ->add('idTitulaire', TextType::class, [
                'label' => 'ID Titulaire',
            ])
            ->add('Adresse', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('AdresseComplExpl', TextType::class, [
                'label' => 'Adresse Complémentaire Expl.',
            ])
            ->add('CodePostExpl', TextType::class, [
                'label' => 'Code Postal Expl.',
            ])
            ->add('NomVilleExpl', TextType::class, [
                'label' => 'Nom de la Ville Expl.',
            ])
            ->add('Complement', TextType::class, [
                'label' => 'Complément',
            ])
            ->add('Tel', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('Fax', TextType::class, [
                'label' => 'Fax',
            ])
            ->add('MedicAccesLibre', CheckboxType::class, [
                'label' => 'Médicament en accès libre',
                'required' => false,
            ])
            ->add('PrescriptionDelivrance', TextType::class, [
                'label' => 'Conditions de prescription et de délivrance',
                'required' => false,
            ])
            // ->add('SignalLie', EntityType::class, [
            //     'class' => Signal::class,
            //     'choice_label' => 'id',
            // ])
            ->add(
                'validation',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary btn-sm m-1'],
                    'label' => 'Validation',
                    'row_attr' => ['id' => 'validation'],
                ]
            )
            ->add(
                'annulation',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary btn-sm m-1'],
                    'label' => 'Annuler',
                    'row_attr' => ['id' => 'annulation'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
