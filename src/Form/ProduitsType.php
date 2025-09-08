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
                'required' => false,
                // 'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('DCI', TextType::class, [
                'label' => 'DCI',
                'required' => false,
                // 'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Dosage', TextType::class, [
                'label' => 'Dosage',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Voie', TextType::class, [
                'label' => 'Voie d\'administration',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('CodeATC', TextType::class, [
                'label' => 'Code ATC',
                'required' => false,
                // 'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('LibATC', TextType::class, [
                'label' => 'Libellé ATC',
                'required' => false,
                // 'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('TypeProcedure', TextType::class, [
                'label' => 'Type de procédure',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('CodeCIS', TextType::class, [
                'label' => 'Code CIS',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('nomProduit', TextType::class, [
                'label' => 'Nom Produit',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci'],
            ])
            // ->add('CodeVU', TextType::class, [
            //     'label' => 'Code VU',
            //     'required' => false,
            //     'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            // ])
            ->add('CodeDossier', TextType::class, [
                'label' => 'Code Dossier',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            // ->add('NomVU', TextType::class, [
            //     'label' => 'Nom VU',
            //     'required' => false,
            //     'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            // ])
            // ->add('Codex', TextType::class, [
            //     'label' => 'Codex',
            //     'required' => false,
            //     'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            // ])
            ->add('Laboratoire', TextType::class, [
                'label' => 'Laboratoire',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('idLaboratoire', TextType::class, [
                'label' => 'ID Laboratoire',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('AdresseContact', TextType::class, [
                'label' => 'Adresse Contact',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('AdresseCompl', TextType::class, [
                'label' => 'Adresse Complémentaire',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('CodePost', TextType::class, [
                'label' => 'Code Postal',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('NomVille', TextType::class, [
                'label' => 'Nom de la Ville',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('TelContact', TextType::class, [
                'label' => 'Téléphone Contact',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('FaxContact', TextType::class, [
                'label' => 'Fax Contact',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('DboPaysLibAbr', TextType::class, [
                'label' => 'Pays',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Titulaire', TextType::class, [
                'label' => 'Titulaire',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('idTitulaire', TextType::class, [
                'label' => 'ID Titulaire',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('AdresseComplExpl', TextType::class, [
                'label' => 'Adresse Complémentaire Expl.',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('CodePostExpl', TextType::class, [
                'label' => 'Code Postal Expl.',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('NomVilleExpl', TextType::class, [
                'label' => 'Nom de la Ville Expl.',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Complement', TextType::class, [
                'label' => 'Complément',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Tel', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('Fax', TextType::class, [
                'label' => 'Fax',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
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
            //     'required' => false,
            // ])
            ->add(
                'validation',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary m-1'],
                    'label' => 'Validation',
                    'row_attr' => ['id' => 'validation'],
                ]
            )
            ->add(
                'annulation',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary m-1'],
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
