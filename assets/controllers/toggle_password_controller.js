import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'iconShow', 'iconHide'];

    connect() {
        // Au chargement, on s'assure que seule l'icône "afficher" est visible
        this.iconHideTarget.classList.add('d-none');
        this.iconShowTarget.classList.remove('d-none');
    }

    toggle() {
        if (this.inputTarget.type === 'password') {
            this.inputTarget.type = 'text';
            this.iconShowTarget.classList.add('d-none');
            this.iconHideTarget.classList.remove('d-none');
        } else {
            this.inputTarget.type = 'password';
            this.iconShowTarget.classList.remove('d-none');
            this.iconHideTarget.classList.add('d-none');
        }
    }
}
