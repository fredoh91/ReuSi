<?php

namespace App\Form;

use App\Entity\ReunionSignal;
use App\Form\LiensReunionsSignalType;
use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReunionSignalDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statutReunion', ChoiceType::class, [
                'label' => 'Statut de la réunion',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'Prévue' => 'prevue',
                    'Réalisée' => 'realisee',
                    'Annulée' => 'annulee',
                ],
            ])
            ->add('fichiers', FileType::class, [
                'label' => 'Ajouter des fichiers',
                'label_attr' => ['class' => 'fw-bold'],
                'multiple' => true,
                'mapped' => false, // Important : ce champ n'est pas lié directement à une propriété de l'entité Signal
                'required' => false,
            ])
            ->add('liensReunionsSignals', CollectionType::class, [
                'entry_type' => LiensReunionsSignalType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // Très important pour que les setters/adders/removers de l'entité soient appelés
                'label' => false,
                'attr' => [
                    'class' => 'liens-reunion-signal-collection', // Classe pour cibler en JS
                ],
            ])
            ->add('Commentaire', TextareaType::class, [
                'label' => 'Commentaire de la réunion',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                ],
            ])            
            ->add('save', SubmitType::class, [
                'label' => $options['save_button_label'],
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReunionSignal::class,
            'save_button_label' => 'Enregistrer les modifications',
        ]);
    }
}
