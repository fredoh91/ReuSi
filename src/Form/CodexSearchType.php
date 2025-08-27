<?php

namespace App\Form;

use App\Entity\Codex\VUUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CodexSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('dci', TextType::class, [
            //     'label' => 'DCI',
            // ])
            // ->add('denomination', TextType::class, [
            //     'label' => 'Dénomination',
            // ])
            ->add(
                'recherche',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary btn-sm m-1'],
                    'label' => 'Rechercher',
                    'row_attr' => ['id' => 'recherche'],
                ]
            )
        
            ->add(
                'reset',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary btn-sm m-1'],
                    'label' => 'Reset',
                    'row_attr' => ['id' => 'reset'],
                ]
            )
            // ->add('codeVU')
            ->add('codeCIS')
            // ->add('codeDossier')
            // ->add('nomVU')
            // ->add('dbo_Autorisation_libAbr')
            ->add('dbo_ClasseATC_libAbr')
            ->add('dbo_ClasseATC_libCourt')
            // ->add('libCourt')
            // ->add('codeContact')
            // ->add('nomContactLibra')
            // ->add('adresseContact')
            // ->add('adresseCompl')
            // ->add('codePost')
            // ->add('nomVille')
            // ->add('telContact')
            // ->add('faxContact')
            // ->add('dbo_Pays_libCourt')
            // ->add('dbo_StatutSpeci_libAbr')
            // ->add('statutAbrege')
            // ->add('codeActeur')
            // ->add('codeTigre')
            // ->add('nomActeurLong')
            // ->add('adresse')
            // ->add('adresseComplExpl')
            // ->add('codePostExpl')
            // ->add('nomVilleExpl')
            // ->add('complement')
            // ->add('tel')
            // ->add('fax')
            // ->add('dbo_Pays_libAbr')
            // ->add('codeProduit')
            ->add('libRechDenomination')
            // ->add('codeVUPrinceps')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => VUUtil::class,
        ]);
    }
}
