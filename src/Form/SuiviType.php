<?php

namespace App\Form;

use App\Entity\ReunionSignal;
use App\Entity\Suivi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviType extends AbstractType
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
            ->add('DescriptionSuivi', TextareaType::class, [
                'label' => 'Description du suivi',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => $options['required_fields']['DescriptionSuivi'] ?? false,
                'disabled' => $options['disabled_fields']['DescriptionSuivi'] ?? false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                ],
            ])
            // ->add('PiloteDS', TextType::class, [
            ->add('PiloteDS', ChoiceType::class, [
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
                'label' => 'Pilote DS',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => $options['required_fields']['PiloteDS'] ?? false,
                'disabled' => $options['disabled_fields']['PiloteDS'] ?? false,
                'attr' => ['class' => 'form-control'],
            ]);

        // Ajout conditionnel du champ de réunion si des réunions sont fournies
        if (!empty($reunions)) {
            $builder->add('reunionSignal', EntityType::class, [
                'class' => ReunionSignal::class,
                'choices' => $reunions,
                'choice_label' => function ($reunion) {
                    return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
                },
                'placeholder' => '-- Sélectionner une réunion --',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => $options['required_fields']['reunionSignal'] ?? false,
                'disabled' => $options['disabled_fields']['reunionSignal'] ?? false,
                'attr' => ['class' => 'form-select'],
            ]);
        }

        $builder->add('EmetteurSuivi', ChoiceType::class, [
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
            'required' => $options['required_fields']['EmetteurSuivi'] ?? false,
            'disabled' => $options['disabled_fields']['EmetteurSuivi'] ?? false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
            'reunions' => [],
            'required_fields' => [],
            'disabled_fields' => [],
        ]);
    }
}
