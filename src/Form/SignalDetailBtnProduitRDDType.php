<?php

namespace App\Form;

use App\Entity\Signal;
use App\Form\SignalDetailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SignalDetailBtnProduitRDDType extends SignalDetailType
{
        public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('ajout_produit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Ajout produit(s)',
                'row_attr' => ['id' => 'ajout_produit'],
            ])
            ->add('ajout_produit_saisie_manu', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Ajout produit(s) manuellement',
                'row_attr' => ['id' => 'ajout_produit_saisie_manu'],
            ])
            ->add('ajout_RDD', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Ajout relevé(s) de décisions',
                'row_attr' => ['id' => 'ajout_RDD'],
            ])
            // ->add('ajout_produit_saisie_manu', SubmitType::class, [
            //     'attr' => ['class' => 'btn btn-secondary m-2'],
            //     'label' => 'Ajout produit(s) manuellement',
            //     'row_attr' => ['id' => 'ajout_produit_saisie_manu'],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
        ]);
    }
}
