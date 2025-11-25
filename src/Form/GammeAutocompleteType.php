<?php

namespace App\Form;

use App\Entity\Gamme;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField] // searchableProperties a été retiré d'ici
class GammeAutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Gamme::class,
            'choice_label' => 'LibGamme',

            // searchable_properties (avec un _) est ajouté ici comme une option
            'searchable_properties' => ['LibGamme', 'Direction', 'PoleCourt', 'PoleLong'],

            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('g')
                    ->where('g.Inactif = :inactif')
                    ->setParameter('inactif', false)
                    ->orderBy('g.Direction', 'ASC')       // Nouveau critère de tri
                    ->addOrderBy('g.PoleTresCourt', 'ASC') // Nouveau critère de tri
                    ->addOrderBy('g.LibGamme', 'ASC');
            },

            'group_by' => function($choice) {
                $direction = $choice->getDirection() ?: 'Autres';
                $poleTresCourt = $choice->getPoleTresCourt(); // Peut être null

                if ($poleTresCourt) {
                    return $direction . ' / ' . $poleTresCourt;
                }
                return $direction;
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
