<?php

namespace App\Form;

use App\Entity\ReunionSignal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalSearchType extends AbstractType
{
    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options [
     *     'data_class' => null,          // Pas de classe de données associée.
     *     'method' => 'GET',             // Méthode HTTP utilisée.
     *     'csrf_protection' => false,    // Désactive la protection CSRF.
     *     'ModeForm' => 'rech_sig_FM'|'rech_ajout_reunion'  // Mode d'affichage.
     *         - 'rech_sig_FM' : Mode complet, utiliser pour la recherche globale (tous les champs).
     *         - 'rech_ajout_reunion' : Mode simplifié, utilisé dans lors de l'ajout d'un suivi/FM a une reunion (masque DCI, Dénomination, Dates, Annulation).
     * ]
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mode = $options['ModeForm'];

        $builder
            ->add('Titre', TextType::class, [
                'required' => false,
                'label' => 'Titre du signal'
            ])
            ->add('Description', TextType::class, [
                'required' => false,
                'label' => 'Contenu (Signal, Suivi, RDD)'
            ]);
            if ($mode === 'rech_sig_FM') {
            $builder
                ->add('Indication', TextType::class, [
                    'required' => false,
                ])
                ->add('Contexte', TextType::class, [
                    'required' => false,
                ])
                ->add('statutSignal', ChoiceType::class, [
                    'label' => 'Statut(s)',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Statut du signal',
                    'choices' => [
                        // 'Brouillon' => 'brouillon',
                        'Clôturé' => 'cloture',
                        // 'En cours de création' => 'en_cours_de_creation',
                        // 'Présenté' => 'presente',
                        // 'Prévu' => 'prevu',
                    ],
                ]);
            }   

        // Affichage des dates de réunion seulement si on n'est pas en mode ajout à une réunion
        // if ($mode !== 'rech_ajout_reunion') {
            $builder
                ->add('dateReunionDebut', DateType::class, [
                    'label' => 'Date réunion (début)',
                    'widget' => 'single_text',
                    'required' => false,
                ])
                ->add('dateReunionFin', DateType::class, [
                    'label' => 'Date réunion (fin)',
                    'widget' => 'single_text',
                    'required' => false,
                ]);
        // }

        // Affichage DCI et Denomination seulement dans le mode complet
        // if ($mode === 'rech_sig_FM') {
        //     $builder
        //         ->add('dci', TextType::class, [
        //             'label' => 'DCI',
        //             'required' => false,
        //         ])
        //         ->add('denomination', TextType::class, [
        //             'label' => 'Dénomination produit',
        //             'required' => false,
        //         ]);
        // }

        $builder
            ->add('medicament', TextType::class, [
                'label' => 'Dénomination / DCI',
                'required' => false,
            ])
            ->add('recherche', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-primary border border-dark']
            ])
            ->add('reset', SubmitType::class, [
                'label' => 'Réinitialiser',
                'attr' => ['class' => 'btn btn-secondary border border-dark']
            ]);

        // Le bouton annuler est masqué en mode ajout_reunion
        if ($mode === 'rech_ajout_reunion') {
            $builder->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => ['class' => 'btn btn-light border border-dark']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
            'csrf_protection' => false,
            'ModeForm' => 'rech_sig_FM', // Mode complet par défaut
            'data' => [
                'statutSignal' => ['brouillon', 'en_cours_de_creation', 'presente', 'prevu']
            ]
        ]);

        $resolver->setAllowedValues('ModeForm', ['rech_sig_FM', 'rech_ajout_reunion']);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}