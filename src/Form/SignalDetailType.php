<?php

namespace App\Form;

use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bundle\SecurityBundle\Security;

class SignalDetailType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // parent::buildForm($builder, $options); // Commenté car AbstractType::buildForm est vide
        
        // Ajout des champs spécifiques à SignalDetailType
        $builder
            ->add('AnaRisqueComment', TextareaType::class, [
                'label' => 'Commentaire de l\'analyse de risque',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
            ])
            ->add('aCloturer', CheckboxType::class, [
                'label' => 'Clôturer ce signal',
                'required' => false,
                'mapped' => false, // Très important ! Car logique complexe dans le service
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label fw-bold text-danger'],
            ]);

        if ($this->security->isGranted('ROLE_REUSI_SURV_EVAL')) {
            $builder->add('nePasAfficherEcranReunion', CheckboxType::class, [
                'label' => 'Ne pas afficher dans l\'écran des réunions',
                'required' => false,
                'mapped' => true, // On laisse Symfony gérer le mapping
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label fw-bold'],
            ]);
        }

        if ($this->security->isGranted('ROLE_REUSI_SURV_EVAL')) {
            $builder->add('TypeSignal', ChoiceType::class, [
                'label' => 'Type',
                'label_attr' => ['class' => 'fw-bold'],
                'choices' => [
                    'Signal' => 'signal',
                    'Fait marquant' => 'fait_marquant',
                ],
                'attr' => ['class' => 'form-select'],
            ]);
        }

        $builder
            ->add('validation', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary m-2'],
            ])
            ->add('annulation', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'class' => 'btn btn-secondary m-2',
                    'formnovalidate' => 'formnovalidate',
                ],
            ]);
        
        // On affiche toujours les boutons Submit dans le builder, 
        // Twig décidera de les afficher ou non si besoin.
        $builder->add('ajout_produit', SubmitType::class, [
            'label' => 'Rechercher un produit',
            'attr' => ['class' => 'btn btn-success m-2'],
        ]);

        $builder->add('ajout_produit_saisie_manu', SubmitType::class, [
            'label' => 'Saisir un produit manuellement',
            'attr' => ['class' => 'btn btn-info m-2'],
        ]);

        $builder->add('ajout_suivi', SubmitType::class, [
            'label' => 'Ajouter un suivi',
            'attr' => ['class' => 'btn btn-warning m-2'],
        ]);

        $builder->add('ajout_mesure', SubmitType::class, [
            'label' => 'Ajouter une mesure',
            'attr' => ['class' => 'btn btn-info m-2'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
            'required_fields' => [],
            'disabled_fields' => [],
            'readonly_fields' => [],
            'add_produit_button' => true,
            'add_produit_saisie_manu_button' => true,
            'add_suivi_button' => true,
            'add_mesure_button' => true,
        ]);
    }
    
    public function getParent(): string
    {
        return SignalType::class;
    }
}
