<?php

namespace App\Form;

use App\Entity\Gamme;
use App\Entity\Signal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Commenté car n'est plus directement utilisé
use Doctrine\ORM\EntityRepository;
// use App\Entity\DirectionPoleConcerne; // Commenté car le champ directionPoleConcernes est remplacé
use App\Form\GammeAutocompleteType; // Ajouté pour le champ Gammes
// use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType; // Commenté car GammeAutocompleteType est utilisé à la place

class SignalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requiredFields = $options['required_fields'];
        $disabledFields = $options['disabled_fields'];
        
        $builder
            ->add('Titre', TextType::class, [
                'label' => 'Titre',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => is_array($requiredFields) ? ($requiredFields['Titre'] ?? true) : true,
                'disabled' => is_array($disabledFields) ? ($disabledFields['Titre'] ?? false) : false,
            ])
            ->add('DescriptionSignal', TextareaType::class, [
                'label' => 'Description du signal',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => is_array($requiredFields) ? ($requiredFields['DescriptionSignal'] ?? true) : true,
                'disabled' => is_array($disabledFields) ? ($disabledFields['DescriptionSignal'] ?? false) : false,
                'attr' => ['rows' => 10, 'class' => 'form-control'],
            ])
            ->add('Indication', TextareaType::class, [
                'label' => 'Indication',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => is_array($requiredFields) ? ($requiredFields['Indication'] ?? false) : false,
                'disabled' => is_array($disabledFields) ? ($disabledFields['Indication'] ?? false) : false,
            ])
            ->add('Contexte', TextareaType::class, [
                'label' => 'Contexte',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => is_array($requiredFields) ? ($requiredFields['Contexte'] ?? false) : false,
                'disabled' => is_array($disabledFields) ? ($disabledFields['Contexte'] ?? false) : false,
            ])
            ->add('SourceSignal', TextType::class, [
                'required' => is_array($requiredFields) ? ($requiredFields['SourceSignal'] ?? false) : false,
                'label' => 'Source du signal',
                'label_attr' => ['class' => 'fw-bold'],
                'disabled' => is_array($disabledFields) ? ($disabledFields['SourceSignal'] ?? false) : false,
                'attr' => [
                ],
            ])            
            ->add('RefSignal', TextType::class, [
                'label' => 'Référence du signal',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => $options['required_fields']['RefSignal'] ?? false,
                'disabled' => $options['disabled_fields']['RefSignal'] ?? false,
                'attr' => (is_array($options['readonly_fields']) && ($options['readonly_fields']['RefSignal'] ?? false)) ? ['readonly' => 'readonly'] : [],
            ])
            ->add('IdentifiantSource', TextType::class, [
                'label' => 'Identifiant source',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => $options['required_fields']['IdentifiantSource'] ?? false,
                'disabled' => $options['disabled_fields']['IdentifiantSource'] ?? false,
            ])
            ->add('NiveauRisqueInitial', ChoiceType::class, [
                'label' => 'Niveau de risque initial',
                'choices' => [
                    'SHR' => 'SHR',
                    'SRI' => 'SRI',
                    'SRM' => 'SRM',
                    'SRF' => 'SRF',
                ],
                'label_attr' => ['class' => 'fw-bold'],
                'required' => $options['required_fields']['NiveauRisqueInitial'] ?? false,
                'disabled' => $options['disabled_fields']['NiveauRisqueInitial'] ?? false,
                'attr' => (is_array($options['readonly_fields']) && ($options['readonly_fields']['NiveauRisqueInitial'] ?? false)) ? ['readonly' => 'readonly'] : [],
            ])
            ->add('NiveauRisqueFinal', ChoiceType::class, [
                'label' => 'Niveau de risque final',
                'choices' => [
                    'SHR' => 'SHR',
                    'SRI' => 'SRI',
                    'SRM' => 'SRM',
                    'SRF' => 'SRF',
                ],
                'label_attr' => ['class' => 'fw-bold'],
                'required' => is_array($requiredFields) ? ($requiredFields['NiveauRisqueFinal'] ?? false) : false,
                'disabled' => is_array($disabledFields) ? ($disabledFields['NiveauRisqueFinal'] ?? false) : false,
                'attr' => (is_array($options['readonly_fields']) && ($options['readonly_fields']['NiveauRisqueFinal'] ?? false)) ? ['readonly' => 'readonly'] : [],
            ])
            /*
            ->add('directionPoleConcernes', EntityType::class, [
                'class' => DirectionPoleConcerne::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->where('d.Inactif = :inactif')
                        ->setParameter('inactif', false)
                        ->orderBy('d.Direction', 'ASC')
                        ->addOrderBy('d.OrdreTri', 'ASC');
                },
                'choice_label' => function ($directionPoleConcerne) {
                    if ($directionPoleConcerne->getPoleCourt()) {
                        return $directionPoleConcerne->getPoleCourt();
                    }
                    return $directionPoleConcerne->getPoleLong() ?: 'Tous pôles';
                },
                'group_by' => function($choice) {
                    return $choice->getDirection() ?: 'Autres';
                },
                'multiple' => true,
                'expanded' => true,
                'required' => is_array($requiredFields) ? ($requiredFields['directionPoleConcernes'] ?? false) : false,
                'disabled' => is_array($disabledFields) ? ($disabledFields['directionPoleConcernes'] ?? false) : false,
                'label' => 'Direction/Pôle concerné(s)',
                'label_attr' => ['class' => 'fw-bold'],
                'attr' => [
                    'class' => 'direction-pole-group',
                ],
            ])
            */
            ->add('gammes', GammeAutocompleteType::class, [
                'multiple' => true,
                'required' => false,
                'placeholder' => '',
                'label' => 'Gamme(s) concernée(s)',
                'label_attr' => ['class' => 'fw-bold'],
                // Les options query_builder, choice_label, group_by sont maintenant dans GammeAutocompleteType
            ]);

        // Ajout des boutons conditionnels
        if ($options['add_produit_button'] ?? false) {
            $builder->add('ajout_produit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Ajouter un produit',
            ]);
        }

        if ($options['add_produit_saisie_manu_button'] ?? false) {
            $builder->add('ajout_produit_saisie_manu', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Saisie manuelle',
            ]);
        }

        if ($options['add_suivi_button'] ?? false) {
            $builder->add('ajout_suivi', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Ajouter un suivi',
            ]);
        }

        if ($options['add_mesure_button'] ?? false) {
            $builder->add('ajout_mesure', SubmitType::class, [
                'attr' => ['class' => 'btn btn-secondary m-2'],
                'label' => 'Ajouter une mesure',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signal::class,
            'required_fields' => [],
            'disabled_fields' => [],
            'readonly_fields' => [],
            'add_produit_button' => false,
            'add_produit_saisie_manu_button' => false,
            'add_suivi_button' => false,
            'add_mesure_button' => false,
        ]);
    }
}
