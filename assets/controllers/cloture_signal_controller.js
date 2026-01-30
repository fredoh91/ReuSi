import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

/**
 * Contrôleur Stimulus pour gérer la confirmation de clôture d'un signal.
 *
 * Ce contrôleur affiche une modale de confirmation lorsqu'un utilisateur
 * coche ou décoche la case "clôturer".
 *
 * Utilisation HTML :
 *
 * <div data-controller="cloture-signal">
 *     <input type="checkbox" data-cloture-signal-target="checkbox" data-action="change->cloture-signal#askConfirmation">
 *
 *     <div class="modal" data-cloture-signal-target="modal">
 *         ...
 *         <button type="button" data-action="cloture-signal#confirm">Confirmer</button>
 *         <button type="button" data-action="cloture-signal#cancel">Annuler</button>
 *     </div>
 * </div>
 */
export default class extends Controller {
    static targets = ["modal", "checkbox"];

    connect() {
        // Initialise l'instance de la modale Bootstrap, en la rendant "statique"
        // pour forcer l'utilisateur à faire un choix.
        this.modal = new Modal(this.modalTarget, {
            keyboard: false,
            backdrop: 'static'
        });

        // Sauvegarde l'état initial de la checkbox lors du chargement de la page.
        this.initialCheckedState = this.checkboxTarget.checked;
    }

    /**
     * Déclenché lorsque l'état de la checkbox change.
     * Affiche la modale de confirmation.
     */
    askConfirmation() {
        this.modal.show();
    }

    /**
     * Déclenché lorsque l'utilisateur clique sur "Confirmer" dans la modale.
     * Le nouvel état de la checkbox est accepté et devient le nouvel état de référence.
     */
    confirm() {
        this.initialCheckedState = this.checkboxTarget.checked;
        this.modal.hide();
    }

    /**
     * Déclenché lorsque l'utilisateur clique sur "Annuler" ou ferme la modale.
     * L'état de la checkbox est restauré à son état précédent.
     */
    cancel() {
        this.checkboxTarget.checked = this.initialCheckedState;
        this.modal.hide();
    }

    /**
     * Nettoyage lors de la déconnexion du contrôleur.
     */
    disconnect() {
        if (this.modal) {
            this.modal.dispose();
        }
    }
}
