<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalRDDDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On utilise un écouteur d'événements pour modifier le formulaire
        // avant que les données ne soient définies.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();

            // Si c'est le RDD initial, on ne veut pas afficher le champ NumeroRDD.
            if ($options['is_initial']) {
                // On retire le champ du formulaire parent s'il existe.
                if ($form->has('NumeroRDD')) {
                    $form->remove('NumeroRDD');
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // On déclare notre nouvelle option 'is_initial' avec une valeur par défaut.
        $resolver->setDefault('is_initial', false);
        $resolver->setAllowedTypes('is_initial', 'bool');
    }

    public function getParent(): string
    {
        // Ce formulaire étend maintenant ReleveDeDecisionType qui contient les champs de RddDetailType
        return ReleveDeDecisionType::class;
    }
}