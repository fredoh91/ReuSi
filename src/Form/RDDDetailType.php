<?php

namespace App\Form;

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

class RDDDetailType extends AbstractType
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
            ->add('NumeroRDD', TextType::class, [
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
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
            // ->add('NiveauRisqueInitial', ChoiceType::class, [
            //     'choices' => [
            //         'SHR' => 'SHR',
            //         'SRI' => 'SRI',
            //         'SRM' => 'SRM',
            //         'SRF' => 'SRF',
            //     ],
            //     'required' => false,
            //     'attr' => [
            //         'class' => 'form-select',
            //         'readonly' => true,
            //         'disabled' => false,
            //     ],
            //     'label' => 'Niveau de risque initial',
            // ])
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
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'Emetteur du RDD',
                'label_attr' => ['class' => 'form-label fw-bold'],
                'required' => false,
            ])
            // ->add('EmetteurSuivi', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //     ],
            //     'label_attr' => ['class' => 'form-label fw-bold'],
            //     'required' => false,
            // ])



            ->add('validation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success px-4'],
                'label' => 'Validation',
                'row_attr' => ['id' => 'validation'],
            ])
            ->add('annulation', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-secondary px-4',
                    'formnovalidate' => true,
                ],
                'label' => 'Annulation',
                'row_attr' => ['id' => 'annulation'],
            ])
            ->add('ajout_mesure', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success px-4',
                    'formnovalidate' => true,
                ],
                'label' => 'Ajout d\'une mesure',
                'row_attr' => ['id' => 'ajout_mesure'],
            ])
        ;
    }

    public function getParent(): string
    {
        return RddPourSuiviType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReleveDeDecision::class,
            'reunions' => [],
        ]);
    }
}
