<?php

namespace App\Form;

use App\Entity\Signal;
use App\Entity\ListeMesures;
use App\Entity\MesuresRDD;
use App\Entity\ReleveDeDecision;
use App\Repository\ListeMesuresRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MesureDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Pré-remplir le Statut à la création
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if ($data && method_exists($data, 'getId') && $data->getId() === null) {
                if (method_exists($data, 'setStatut')) {
                    $data->setStatut('en_cours');
                }
            }
        });

        $builder
            ->add('LibMesure', EntityType::class, [
                'class' => ListeMesures::class,
                'query_builder' => function (ListeMesuresRepository $er) {
                    return $er->findActiveSortedQueryBuilder();
                },
                'choice_label' => 'LibMesure',
                'choice_value' => 'LibMesure', // Important: on stocke la chaîne, pas l'ID de l'entité
                'placeholder' => '--- Sélectionner une mesure ---',
                'required' => true,
                'label' => 'Mesure',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'mapped' => false, // On gère manuellement la donnée dans le contrôleur
            ])
            ->add('DetailCommentaire', TextareaType::class, [
                'label' => 'Détail de la mesure',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            // ->add('DateCloturePrev', DateType::class, [
            //     'widget' => 'single_text',
            //     'label' => 'Date de clôture prévisionnelle',
            //     'required' => false,
            //     'attr' => ['class' => 'form-control'],
            //     'label_attr' => ['class' => 'form-label fw-bold'],
            // ])
            // ->add('DateClotureEffective', DateType::class, [
            //     'widget' => 'single_text',
            //     'label' => 'Date de clôture effective',
            //     'required' => false,
            //     'attr' => ['class' => 'form-control'],
            //     'label_attr' => ['class' => 'form-label fw-bold'],
            // ])
            ->add('DatePrevisionnelle', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date prévisionnelle',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('DateMiseEnOeuvre', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de mise en œuvre',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            // Le champ Statut est ajouté dynamiquement via l'écouteur PRE_SET_DATA ci-dessous
            
            ->add('validation', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success px-4'],
                'label' => 'Valider',
                'row_attr' => ['id' => 'validation'],
            ])
            ->add('annulation_mesure', SubmitType::class, [
                'attr' => ['class' => 'btn btn-danger px-4'],
                'label' => 'Annuler cette mesure',
                'row_attr' => ['id' => 'annulation_mesure'],
            ])
            ->add('annulation', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-secondary px-4',
                    'formnovalidate' => true,
                ],
                'label' => 'Fermer',
                'row_attr' => ['id' => 'annulation'],
            ])
        ;

        // Gestion dynamique du champ Statut
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $mesure = $event->getData();
            $form = $event->getForm();
            $isLatest = $options['is_latest_suivi'];

            $choices = [
                'en cours' => 'en_cours',
                'effectuée' => 'effectuee',
            ];

            // On inclut 'annulée' et 'historisée' seulement si c'est la valeur actuelle,
            // car ils sont gérés par le système et ne doivent pas être sélectionnés manuellement sinon.
            if ($mesure instanceof MesuresRDD && $mesure->getStatut()) {
                $currentStatut = $mesure->getStatut();
                if ($currentStatut === 'annulee') {
                    $choices['annulée'] = 'annulee';
                } elseif ($currentStatut === 'historisee') {
                    $choices['historisée'] = 'historisee';
                }
            }

            $form->add('Statut', ChoiceType::class, [
                'label' => 'Statut',
                'required' => false,
                'choices' => $choices,
                'placeholder' => '--- Sélectionner ---',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label fw-bold'],
                'disabled' => !$isLatest,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MesuresRDD::class,
            'is_latest_suivi' => false,
        ]);

        $resolver->setAllowedTypes('is_latest_suivi', 'bool');
    }
}
