<?php

namespace App\Service;

use App\Entity\Signal;
use App\Entity\StatutSignal;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;

class SignalStatusService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Passe le statut du signal à "prevu" si les conditions sont remplies.
     *
     * @param Signal $signal Le signal à mettre à jour.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     */
    public function updateToPrevuIfNeeded(Signal $signal, string $userName): void
    {
        $statutActif = $this->entityManager->getRepository(StatutSignal::class)
            ->findOneBy(['SignalLie' => $signal, 'StatutActif' => true]);

        // On ne change le statut que si le statut actuel est "brouillon"
        if ($statutActif && $statutActif->getLibStatut() === 'brouillon') {
            
            // Vérifier si au moins un suivi a une réunion d\'associée
            $hasReunion = false;
            foreach ($signal->getSuivis() as $suivi) {
                if ($suivi->getReunionSignal() !== null) {
                    $hasReunion = true;
                    break;
                }
            }

            if ($hasReunion) {
                // Désactiver l\'ancien statut "brouillon"
                $statutActif->setStatutActif(false);
                $statutActif->setDateDesactivation(new DateTimeImmutable());
                $statutActif->setUpdatedAt(new DateTimeImmutable());
                $statutActif->setUserModif($userName);
                $this->entityManager->persist($statutActif);

                // Créer le nouveau statut "prevu"
                $nouveauStatut = new StatutSignal();
                $nouveauStatut->setLibStatut('prevu');
                $nouveauStatut->setDateMiseEnPlace(new DateTimeImmutable());
                $nouveauStatut->setStatutActif(true);
                $nouveauStatut->setSignalLie($signal);
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
     * Passe le statut du signal à "prevu" si les conditions sont remplies.
     *
     * @param Signal $signal Le signal à mettre à jour.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     */
    public function updateToPresenteIfNeeded(Signal $signal, string $userName): void
    {
        $statutActif = $this->entityManager->getRepository(StatutSignal::class)
            ->findOneBy(['SignalLie' => $signal, 'StatutActif' => true]);

        // On ne change le statut que si le statut actuel est "brouillon"
        if ($statutActif && $statutActif->getLibStatut() !== 'presente') {
            
            // Vérifier si au moins un suivi a une réunion d\'associée
            $hasReunion = false;
            foreach ($signal->getSuivis() as $suivi) {
                if ($suivi->getReunionSignal() !== null) {
                    $hasReunion = true;
                    break;
                }
            }

            if ($hasReunion) {
                // Désactiver l\'ancien statut "brouillon"
                $statutActif->setStatutActif(false);
                $statutActif->setDateDesactivation(new DateTimeImmutable());
                $statutActif->setUpdatedAt(new DateTimeImmutable());
                $statutActif->setUserModif($userName);
                $this->entityManager->persist($statutActif);

                // Créer le nouveau statut "presente"
                $nouveauStatut = new StatutSignal();
                $nouveauStatut->setLibStatut('presente');
                $nouveauStatut->setDateMiseEnPlace(new DateTimeImmutable());
                $nouveauStatut->setStatutActif(true);
                $nouveauStatut->setSignalLie($signal);
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
     * Passe le statut du signal à "cloture".
     *
     * @param Signal $signal Le signal à clôturer.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     */
    public function clotureSignal(Signal $signal, string $userName): void
    {
        $statutActif = $this->entityManager->getRepository(StatutSignal::class)
            ->findOneBy(['SignalLie' => $signal, 'StatutActif' => true]);

        // On ne clôture pas un signal déjà clôturé
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
        $nouveauStatut = new StatutSignal();
        $nouveauStatut->setLibStatut('cloture');
        $nouveauStatut->setDateMiseEnPlace(new DateTimeImmutable());
        $nouveauStatut->setStatutActif(true);
        $nouveauStatut->setSignalLie($signal);
        $nouveauStatut->setCreatedAt(new DateTimeImmutable());
        $nouveauStatut->setUserCreate($userName);
        $nouveauStatut->setUpdatedAt(new DateTimeImmutable());
        $nouveauStatut->setUserModif($userName);

        $this->entityManager->persist($nouveauStatut);
        $this->entityManager->flush();
    }


    public function findLastStatutBySignal($signalIdvalue): ?StatutSignal
    {
        return $this->entityManager->getRepository(StatutSignal::class)
            ->findLastStatutBySignal($signalIdvalue);
    }
}