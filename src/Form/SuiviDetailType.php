<?php

namespace App\Form;

use App\Entity\Suivi;
use App\Entity\Signal;
use App\Entity\ReunionSignal;
use App\Entity\ReleveDeDecision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SuiviDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère la liste des réunions passée depuis le contrôleur.
        $reunions = $options['reunions'] ?? [];

        // Trie les réunions de la plus récente à la plus ancienne.
        usort($reunions, function ($a, $b) {
            if (!$a->getDateReunion() || !$b->getDateReunion()) {
                return 0;
            }
            return $b->getDateReunion() <=> $a->getDateReunion();
        });


        $builder
            // ->add('NumeroSuivi', TextType::class, [
            //     'label' => 'Numéro de suivi',
            //     'disabled' => true,
            // ])
            
            ->add('NumeroSuivi', TextType::class, [
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            // ->add('reunionSignal', EntityType::class, [
            //     'class' => ReunionSignal::class,
            //     'choices' => $options['date_reunion'] ?? [],
            //     'choice_label' => function ($reunion) {
            //         return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
            //     },
            //     'placeholder' => '-- Choisir une réunion --',
            //     'required' => false,
            //     'label' => 'Date de la réunion',
            // ])
            ->add('reunionSignal', EntityType::class, [
                'class' => ReunionSignal::class,
                'choices' => $reunions,
                'choice_label' => function ($reunion) {
                    return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
                },
                'placeholder' => '-- Sélectionner une réunion --',
                // 'required' => true,
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            ->add('DescriptionSuivi', TextareaType::class, [
                'label' => 'Description du suivi',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            // ->add('DescriptionSuivi', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'rows' => 3,
            //     ],
            //     'label_attr' => ['class' => 'form-label fw-bold'],
            //     'required' => false,
            // ])
            ->add('PiloteDS', TextType::class, [ // Sera un ChoiceType plus tard
                'label' => 'Pilote DS',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            ->add('EmetteurSuivi', ChoiceType::class, [
                'choices' => [
                    'PGS' => 'PGS',
                    'PP' => 'PP',
                    'PS' => 'PS',
                    'RGA' => 'RGA',
                    'EC' => 'EC',
                    'Addicto' => 'ADDICTO',
                    'EM' => 'EM',
                    'UNC' => 'UNC',
                    'Dir. SURV' => 'DIR_SURV',
                ],
                'label' => 'Émetteur du suivi',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            // ->add('UserCreate')
            // ->add('UserModif')
            // ->add('CreatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UpdatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('EmetteurSuivi')
            ->add('validation', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => ['class' => 'btn btn-secondary'],
            ]);
            // ->add('SignalLie', EntityType::class, [
            //     'class' => Signal::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('reunionSignal', EntityType::class, [
            //     'class' => ReunionSignal::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('RddLie', EntityType::class, [
            //     'class' => ReleveDeDecision::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
            'reunions' => [],
        ]);
    }
}
