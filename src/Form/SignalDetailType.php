<?php

namespace App\Form;

use App\Entity\DirectionPoleConcerne;
use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class SignalDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre', TextType::class, [
                'label' => 'Titre',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('DescriptionSignal', TextareaType::class, [
                'label' => 'Description du signal',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('Indication', TextareaType::class, [
                'label' => 'Indication',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('Contexte', TextareaType::class, [
                'label' => 'Contexte',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('SourceSignal', TextType::class, [
                'required' => false,
                'label' => 'Source du signal',
                'label_attr' => ['class' => 'fw-bold'],
                'attr' => [
                    'data-controller' => 'autocomplete',
                ],
            ])            
            ->add('RefSignal', TextType::class, [
                'label' => 'Référence du signal',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('IdentifiantSource', TextType::class, [
                'label' => 'Identifiant source',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('NiveauRisqueInitial', ChoiceType::class, [
                'choices' => [
                    'SHR' => 'SHR',
                    'SRI' => 'SRI',
                    'SRM' => 'SRM',
                    'SRF' => 'SRF',
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'readonly' => true,
                    'disabled' => false,
                ],
                'label' => 'Niveau de risque initial',
                'label_attr' => ['class' => 'fw-bold'],
            ])
            ->add('NiveauRisqueFinal', ChoiceType::class, [
                'choices' => [
                    'SHR' => 'SHR',
                    'SRI' => 'SRI',
                    'SRM' => 'SRM',
                    'SRF' => 'SRF',
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'readonly' => true,
                    'disabled' => false,
                ],
                'label' => 'Niveau de risque final',
                'label_attr' => ['class' => 'fw-bold'],
            ])
            ->add('directionPoleConcernes', EntityType::class, [
                'class' => DirectionPoleConcerne::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->where('d.Inactif = :inactif')
                        ->setParameter('inactif', false)
                        ->orderBy('d.OrdreTri', 'ASC');
                },
                'choice_label' => function ($directionPoleConcerne) {
                    $poleCourt = $directionPoleConcerne->getPoleCourt();
                    // Si PoleCourt est vide ou ne contient que des espaces, on affiche la Direction.
                    if (empty(trim((string) $poleCourt))) {
                        return $directionPoleConcerne->getDirection();
                    }
                    return $poleCourt;
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Directions / Pôles concernés',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
                'group_by' => function($choice, $key, $value) {
                    return $choice->getDirection();
                },
            ])
            ->add('AnaRisqueComment', TextareaType::class, [
                'label' => 'Commentaire de l\'analyse de risque',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('validation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Validation',
                'row_attr' => ['id' => 'validation'],
            ])
            ->add('annulation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-2'],
                'label' => 'Annulation',
                'row_attr' => ['id' => 'annulation'],
            ])
        ;

        if ($options['add_produit_button']) {
            $builder->add('ajout_produit', SubmitType::class, [
                'label' => 'Rechercher un produit',
                'attr' => ['class' => 'btn btn-success m-2'],
            ]);
        }

        if ($options['add_produit_saisie_manu_button']) {
            $builder->add('ajout_produit_saisie_manu', SubmitType::class, [
                'label' => 'Saisir un produit manuellement',
                'attr' => ['class' => 'btn btn-info m-2'],
            ]);
        }

        if ($options['add_suivi_button']) {
            $builder->add('ajout_suivi', SubmitType::class, [
                'label' => 'Ajouter un suivi',
                'attr' => ['class' => 'btn btn-warning m-2'],
            ]);
        }

        if ($options['add_mesure_button']) {
            $builder->add('ajout_mesure', SubmitType::class, [
                'label' => 'Ajouter une mesure',
                'attr' => ['class' => 'btn btn-info m-2'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
            'add_produit_button' => false,
            'add_produit_saisie_manu_button' => false,
            'add_suivi_button' => false,
            'add_mesure_button' => false,
        ]);
    }
}