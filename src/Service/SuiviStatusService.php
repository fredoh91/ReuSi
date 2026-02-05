<?php

namespace App\Service;

use App\Entity\Suivi;
use App\Entity\StatutSuivi;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;

class SuiviStatusService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Passe le statut du suivi à "prevu" si les conditions sont remplies.
     *
     * @param Suivi $suivi Le suivi à mettre à jour.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     */
    public function updateToPrevuIfNeeded(Suivi $suivi, string $userName): void
    {
        $statutActif = $this->entityManager->getRepository(StatutSuivi::class)
            ->findOneBy(['SuiviLie' => $suivi, 'StatutActif' => true]);

        // On ne change le statut que si le statut actuel est "brouillon"
        if ($statutActif && $statutActif->getLibStatut() === 'brouillon') {
            
            // Est ce que la suivi a une réunion d'associée
            // $hasReunion = false;
            // if ($suivi->getReunionSignal() !== null) {
            //     $hasReunion = true;
            // }
            $hasReunion = ($suivi->getReunionSignal() !== null ? true : false);

            if ($hasReunion) {
                // Désactiver l\'ancien statut "brouillon"
                $statutActif->setStatutActif(false);
                $statutActif->setDateDesactivation(new DateTimeImmutable());
                $statutActif->setUpdatedAt(new DateTimeImmutable());
                $statutActif->setUserModif($userName);
                $this->entityManager->persist($statutActif);

                // Créer le nouveau statut "prevu"
                $nouveauStatut = new StatutSuivi();
                $nouveauStatut->setLibStatut('prevu');
                $nouveauStatut->setDateMiseEnPlace(new DateTimeImmutable());
                $nouveauStatut->setStatutActif(true);
                $nouveauStatut->setSuiviLie($suivi);
                $nouveauStatut->setCreatedAt(new DateTimeImmutable());
                $nouveauStatut->setUserCreate($userName);
                $nouveauStatut->setUpdatedAt(new DateTimeImmutable());
                $nouveauStatut->setUserModif($userName);
                
                $this->entityManager->persist($nouveauStatut);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * Passe le statut du suivi à "prevu" si les conditions sont remplies.
     *
     * @param Suivi $suivi Le suivi à mettre à jour.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     * @param string $statut Le nouveau statut à appliquer.
     */
    public function updateStatutSuivi(Suivi $suivi, string $userName, string $statut): void
    {

        if($statut) {
            $statutActif = $this->entityManager->getRepository(StatutSuivi::class)
                ->findOneBy(['SuiviLie' => $suivi, 'StatutActif' => true]);
    
            // Désactiver l\'ancien statut 
            $statutActif->setStatutActif(false);
            $statutActif->setDateDesactivation(new DateTimeImmutable());
            $statutActif->setUpdatedAt(new DateTimeImmutable());
            $statutActif->setUserModif($userName);
            $this->entityManager->persist($statutActif);
    
            // Créer le nouveau statut "prevu"
            $nouveauStatut = new StatutSuivi();
            $nouveauStatut->setLibStatut($statut);
            $nouveauStatut->setDateMiseEnPlace(new DateTimeImmutable());
            $nouveauStatut->setStatutActif(true);
            $nouveauStatut->setSuiviLie($suivi);
            $nouveauStatut->setCreatedAt(new DateTimeImmutable());
            $nouveauStatut->setUserCreate($userName);
            $nouveauStatut->setUpdatedAt(new DateTimeImmutable());
            $nouveauStatut->setUserModif($userName);
            
            $this->entityManager->persist($nouveauStatut);
            $this->entityManager->flush();
        } else {
            throw new \InvalidArgumentException('Le statut ne peut pas être vide.');
        }
    }

    /**
     * Passe le statut du suivi à "cloture".
     *
     * @param Suivi $suivi Le suivi à clôturer.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     */
    public function clotureSuivi(Suivi $suivi, string $userName): void
    {
        $statutActif = $this->entityManager->getRepository(StatutSuivi::class)
            ->findOneBy(['SuiviLie' => $suivi, 'StatutActif' => true]);

        // On ne clôture pas un suivi déjà clôturé
        if ($statutActif && $statutActif->getLibStatut() === 'cloture') {
            return;
        }

        if ($statutActif) {
            // Désactiver l\'ancien statut
            $statutActif->setStatutActif(false);
            $statutActif->setDateDesactivation(new DateTimeImmutable());
            $statutActif->setUpdatedAt(new DateTimeImmutable());
            $statutActif->setUserModif($userName);
            $this->entityManager->persist($statutActif);
        }

        // Créer le nouveau statut "cloture"
        $nouveauStatut = new StatutSuivi();
        $nouveauStatut->setLibStatut('cloture');
        $nouveauStatut->setDateMiseEnPlace(new DateTimeImmutable());
        $nouveauStatut->setStatutActif(true);
        $nouveauStatut->setSuiviLie($suivi);
        $nouveauStatut->setCreatedAt(new DateTimeImmutable());
        $nouveauStatut->setUserCreate($userName);
        $nouveauStatut->setUpdatedAt(new DateTimeImmutable());
        $nouveauStatut->setUserModif($userName);

        $this->entityManager->persist($nouveauStatut);
        $this->entityManager->flush();
    }


    public function findLastStatutBySuivi($suiviIdvalue): ?StatutSuivi
    {
        return $this->entityManager->getRepository(StatutSuivi::class)
            ->findLastStatutBySuivi($suiviIdvalue);
    }
}