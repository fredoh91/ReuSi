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

class SuiviInitialType extends AbstractType
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
            ->add('DescriptionSuivi', TextareaType::class, [
                'label' => 'Description du suivi',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                ],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ])
            ->add('PiloteDS', TextType::class, [ // Sera un ChoiceType plus tard
                'label' => 'Pilote DS',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ])
            ->add('reunionSignal', EntityType::class, [
                'class' => ReunionSignal::class,
                'choices' => $reunions,
                'choice_label' => function ($reunion) {
                    return $reunion->getDateReunion() ? $reunion->getDateReunion()->format('d/m/Y') : 'Date inconnue';
                },
                'placeholder' => '-- Sélectionner une réunion --',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
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
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
            'reunions' => [],
        ]);
    }
}
