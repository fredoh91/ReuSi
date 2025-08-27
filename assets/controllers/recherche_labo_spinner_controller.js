import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['spinner'];

    connect() {
        if (this.hasSpinnerTarget) {
            this.spinnerTarget.classList.add('d-none');
        }
    }

    showSpinner(event) {
        // Affiche le spinner
        this.spinnerTarget.classList.remove('d-none');
        // Récupère le message du bouton cliqué
        const message = event.currentTarget.getAttribute('data-message') || 'Recherche en cours ...';
        this.spinnerTarget.querySelector('.mt-2').textContent = message;
    }

    hideSpinner(event) {
        if (this.hasSpinnerTarget) {
            this.spinnerTarget.classList.add('d-none');
        }
    }
}
