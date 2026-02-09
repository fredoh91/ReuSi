<?php

namespace App\Service;

use App\Entity\Suivi;
use App\Entity\Signal;
use App\Entity\StatutSignal;
use App\Entity\StatutSuivi;
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
     * Passe le statut du signal à "presente" si les conditions sont remplies.
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
    public function updateToClotureIfNeeded(Signal $signal, string $userName): void
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


    /**
     * Passe le statut du signal au statut avant la cloture, il redevient ainsi actif.
     *
     * @param Signal $signal Le signal à clôturer.
     * @param string $userName Le nom de l\'utilisateur effectuant l\'action.
     */
    public function updateToDernierStatutAvantCloture(Signal $signal, string $userName): void
    {
        $statutActif = $this->entityManager->getRepository(StatutSignal::class)
            ->findOneBy(['SignalLie' => $signal, 'StatutActif' => true]);

        // On ne doit traiter qu'un signal actuellement clôturé
        if ($statutActif && $statutActif->getLibStatut() !== 'cloture') {
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

        // On recherche le dernier statut qui n'est pas "cloture"
        $qb = $this->entityManager->getRepository(StatutSignal::class)->createQueryBuilder('s');
        $statutAvantCloture = $qb
            ->where('s.SignalLie = :signal')
            ->andWhere('s.LibStatut != :cloture')
            ->setParameter('signal', $signal)
            ->setParameter('cloture', 'cloture')
            ->orderBy('s.DateMiseEnPlace', 'DESC')
            ->addOrderBy('s.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $nouveauStatut = new StatutSignal();
        $nouveauStatut->setLibStatut($statutAvantCloture ? $statutAvantCloture->getLibStatut() : 'brouillon');
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

    /**
     * Détermine un statut consolidé pour l'affichage dans les listes.
     *
     * @param Signal $signal Le signal à analyser.
     * @return string Le statut calculé ('cloture', 'prevu', 'presente', ou 'en_cours').
     */
    public function donneStatutSignalSuivi(Signal $signal): string
    {
        // Règle 1: Si le statut du signal est 'cloture'
        $lastStatutSignal = $this->findLastStatutBySignal($signal->getId());
        if ($lastStatutSignal && $lastStatutSignal->getLibStatut() === 'cloture') {
            return 'cloture';
        }

        // Règle 2 & 3: Analyser le dernier suivi
        /** @var \App\Repository\SuiviRepository $suiviRepo */
        $suiviRepo = $this->entityManager->getRepository(Suivi::class);
        $latestSuivi = $suiviRepo->findLatestForSignal($signal);

        if ($latestSuivi) {
            /** @var \App\Repository\StatutSuiviRepository $statutSuiviRepo */
            $statutSuiviRepo = $this->entityManager->getRepository(StatutSuivi::class);
            $lastStatutSuivi = $statutSuiviRepo->findLastStatutBySuivi($latestSuivi->getId());

            if ($lastStatutSuivi) {
                $libStatut = $lastStatutSuivi->getLibStatut();
                if (in_array($libStatut, ['prevu', 'presente'])) {
                    return $libStatut;
                }
            }
        }

        // Statut par défaut si aucune des règles ci-dessus ne s'applique
        return 'en_cours';
    }
}