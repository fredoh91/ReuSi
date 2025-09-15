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

class MesureDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                'label' => 'Détail / Commentaire',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('DateCloturePrev', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de clôture prévisionnelle',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('DateClotureEffective', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de clôture effective',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            // ->add('DesactivateAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UserCreate')
            // ->add('UserModif')
            // ->add('CreatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UpdatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('RddLie', EntityType::class, [
            //     'class' => ReleveDeDecision::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('SignalLie', EntityType::class, [
            //     'class' => Signal::class,
            //     'choice_label' => 'id',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MesuresRDD::class,
        ]);
    }
}
