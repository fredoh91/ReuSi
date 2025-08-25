<?php

namespace App\Form;

use App\Entity\ReleveDeDecision;
use App\Entity\ReunionSignal;
use App\Entity\Signal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RDDDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NumeroRDD', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('DescriptionRDD')
            // ->add('UserCreate')
            // ->add('UserModif')
            // ->add('CreatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('UpdatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('SignalLie', EntityType::class, [
            //     'class' => Signal::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('reunionSignal', EntityType::class, [
            //     'class' => ReunionSignal::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReleveDeDecision::class,
        ]);
    }
}
