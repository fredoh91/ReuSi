<?php

namespace App\Form;

use App\Entity\Produits;
use App\Form\ProduitsBaseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitsMedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ])
            ->add('LibATC', TextType::class, [
                'label' => 'Libellé ATC',
                'required' => false,
            ])
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
            ->add('nomProduit', TextType::class, [
                'label' => 'Nom Produit',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
            ])
            ->add('CodeDossier', TextType::class, [
                'label' => 'Code Dossier',
                'required' => false,
                'attr' => ['class' => 'Chp-a-effacer-dci Chp-a-effacer-prod'],
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
            ->add('formatDCI', ButtonType::class, [
                'label' => 'Formatage DCI',
                'attr' => [
                    'class' => 'btn btn-formatage-dci m-1',
                    // Action Stimulus pour le formatage DCI 
                    'data-action' => 'click->format-produit-recherche#formatDCI'
                ],
            ])
            ->add('formatProduit', ButtonType::class, [
                'label' => 'Formatage Produit',
                'attr' => [
                    'class' => 'btn btn-formatage-produit m-1',
                    // Action Stimulus pour le formatage Produit
                    'data-action' => 'click->format-produit-recherche#formatProduit'
                ],
            ]);
    }

    public function getParent(): string
    {
        return ProduitsBaseType::class;
    }

}
