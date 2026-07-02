<?php

namespace App\Service;

use App\Entity\Signal;
use App\Entity\Suivi;
use App\Entity\ReleveDeDecision;
use App\Entity\ReunionSignal;
use Doctrine\ORM\EntityManagerInterface;

class ReunionSignalSyncService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Synchronise les trois liaisons de ReunionSignal pour un signal donné
     * 
     * Les liaisons à synchroniser sont :
     * 1. signal->suiviInitial->ReunionSignal
     * 2. signal->ReunionSignal
     * 3. signal->suiviInitial->RDD->ReunionSignal
     *
     * @param Signal $signal Le signal concerné
     * @param Suivi $suivi Le suivi
     * @param ReleveDeDecision $rdd Le RDD 
     * @param ReunionSignal|null $reunionSignal La réunion signal sélectionnée
     */
    public function synchronizeReunionSignalLinks(
        Signal $signal,
        ?Suivi $suivi = null,
        ?ReleveDeDecision $rdd = null,
        ?ReunionSignal $reunionSignal = null
    ): void {
        // 1. Mettre à jour signal->suiviInitial->ReunionSignal
        if ($suivi !== null) {
            $suivi->setReunionSignal($reunionSignal);
        }
        
        /* Code d'origine mis en commentaire :
        // 2. Mettre à jour signal->ReunionSignal (si nécessaire)
        // Vérifier si la réunion signal n'est pas déjà présente
        if ($reunionSignal !== null) {
            $existingReunionSignals = $signal->getReunionSignals();
            $alreadyExists = false;
            
            if ($existingReunionSignals !== null) {
                foreach ($existingReunionSignals as $existingReunion) {
                    if ($existingReunion->getId() === $reunionSignal->getId()) {
                        $alreadyExists = true;
                        break;
                    }
                }
            }
            
            // Ajouter seulement si la réunion n'existe pas déjà
            if (!$alreadyExists && $existingReunionSignals !== null) {
                $signal->addReunionSignal($reunionSignal);
            } elseif ($existingReunionSignals === null) {
                // Si la collection n'existe pas encore, on l'initialise
                $signal->addReunionSignal($reunionSignal);
            }
        }
        */
        
        // 3. Mettre à jour signal->suiviInitial->RDD->ReunionSignal
        // Assurer que le RDD initial a la bonne réunion signal
        if ($rdd !== null) {
            $rdd->setReunionSignal($reunionSignal);
        }

        // --- NOUVELLE LOGIQUE DE LA MÉTHODE A ---
        // On recalcule entièrement la collection ManyToMany de réunions du signal
        // afin de garantir la cohérence et d'éliminer les réunions qui ne sont plus liées à aucun suivi/RDD.
        
        // a. On vide la collection ManyToMany proprement
        $currentReunions = $signal->getReunionSignals();
        if ($currentReunions !== null) {
            foreach ($currentReunions->toArray() as $existingReunion) {
                $signal->removeReunionSignal($existingReunion);
            }
        }

        // b. On parcourt tous les suivis pour rajouter les réunions associées
        $suivis = $signal->getSuivis();
        if ($suivis !== null) {
            foreach ($suivis as $s) {
                $reu = $s->getReunionSignal();
                if ($reu !== null) {
                    $signal->addReunionSignal($reu);
                }
            }
        }

        // c. On parcourt également tous les RDD pour rajouter les réunions associées (au cas où certains n'auraient pas de suivi)
        $rdds = $signal->getReleveDeDecision();
        if ($rdds !== null) {
            foreach ($rdds as $r) {
                $reu = $r->getReunionSignal();
                if ($reu !== null) {
                    $signal->addReunionSignal($reu);
                }
            }
        }

        // d. On s'assure d'ajouter également la réunion en cours de synchronisation
        // (utile si le suivi/RDD n'est pas encore persisté et n'apparaît pas dans les collections du signal)
        if ($reunionSignal !== null) {
            $signal->addReunionSignal($reunionSignal);
        }
    }
}
