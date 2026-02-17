<?php

namespace App\Form;

use App\Entity\LiensReunionsSignal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiensReunionsSignalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé du lien',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'label' => 'URL',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LiensReunionsSignal::class,
        ]);
    }
}
