<?php

namespace App\Form;

use App\Entity\ReleveDeDecision;
use App\Entity\ReunionSignal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReleveDeDecisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $reunions = $options['reunions'] ?? [];

        // Trie les réunions de la plus récente à la plus ancienne.
        usort($reunions, function ($a, $b) {
            if (!$a->getDateReunion() || !$b->getDateReunion()) {
                return 0;
            }
            return $b->getDateReunion() <=> $a->getDateReunion();
        });

        $builder
            // ->add('NumeroRDD', TextType::class, [
            //     'label' => 'Numéro du RDD',
            //     'label_attr' => ['class' => 'form-label fw-bold'],
            //     'attr' => ['class' => 'form-control'],
            //     // 'required' => $options['required_fields']['NumeroRDD'] ?? true,
            //     'disabled' => $options['disabled_fields']['NumeroRDD'] ?? false,
            //     'required' => true,
            // ])
            ->add('DescriptionRDD', TextareaType::class, [
                'label' => 'Description du relevé de décision',
                'label_attr' => ['class' => 'form-label fw-bold'],
                // 'required' => $options['required_fields']['DescriptionRDD'] ?? true,
                // 'disabled' => $options['disabled_fields']['DescriptionRDD'] ?? false,
                'required' => false,
                'disabled' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                ],
                
            ])
            ->add('PassageCTP', ChoiceType::class, [
                'choices' => [
                    'Oui' => 'oui',
                    'Non' => 'non',
                    'En cours d\'évaluation' => 'en_cours_d_evaluation',
                ],
                'label' => 'Passage en CTP',
                'label_attr' => ['class' => 'form-label fw-bold'],
                // 'required' => $options['required_fields']['PassageCTP'] ?? true,
                'required' => false,
                'disabled' => $options['disabled_fields']['PassageCTP'] ?? false,
                'attr' => ['class' => 'form-select'],
                'placeholder' => '',
            ])
            ->add('PassageRSS', ChoiceType::class, [
                'choices' => [
                    'Oui' => 'oui',
                    'Non' => 'non',
                    'En cours d\'évaluation' => 'en_cours_d_evaluation',
                ],
                'label' => 'Passage en RSS',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'attr' => ['class' => 'form-select'],
                'placeholder' => '',
                // 'required' => $options['required_fields']['PassageRSS'] ?? false,
                'required' => false,
                'disabled' => $options['disabled_fields']['PassageRSS'] ?? false,
            ])
            ;

        // // Ajout conditionnel du champ de réunion si des réunions sont fournies
        // if (!empty($reunions)) {
        //     $builder->add('reunionSignal', EntityType::class, [
        //         'class' => ReunionSignal::class,
        //         'choices' => $reunions,
        //         'choice_label' => function ($reunion) {
        //             return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
        //         },
        //         'placeholder' => '-- Sélectionner une réunion --',
        //         'label_attr' => ['class' => 'form-label fw-bold'],
        //         'required' => $options['required_fields']['reunionSignal'] ?? false,
        //         'disabled' => $options['disabled_fields']['reunionSignal'] ?? false,
        //         'attr' => ['class' => 'form-select'],
        //     ]);
        // }

        // Champ pour le numéro de RDD (généralement en lecture seule)
        if ($options['show_numero_rdd'] ?? false) {
            $builder->add('NumeroRDD', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => $options['required_fields']['NumeroRDD'] ?? false,
                'disabled' => $options['disabled_fields']['NumeroRDD'] ?? true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReleveDeDecision::class,
            'required_fields' => [
                // 'NumeroRDD' => false,  // Le numéro RDD n'est pas obligatoire car il est généré automatiquement
                'DescriptionRDD' => true,
                'PassageCTP' => true,
                // 'reunionSignal' => false,
                'PassageRSS' => false,
            ],
            'disabled_fields' => [
                // 'NumeroRDD' => true,   // Le numéro RDD est en lecture seule
                'DescriptionRDD' => false,
                'PassageCTP' => false,
                // 'reunionSignal' => false,
                'PassageRSS' => false,
            ],
            'show_numero_rdd' => false,
            'reunions' => [],
        ]);
    }
}
