import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['first', 'second', 'message'];

    checkMatch() {
        const firstValue = this.firstTarget.value;
        const secondValue = this.secondTarget.value;

        if (secondValue.length === 0) {
            this.messageTarget.textContent = '';
            this.messageTarget.className = '';
            return;
        }

        if (firstValue === secondValue) {
            this.messageTarget.textContent = '✓ Les mots de passe correspondent';
            this.messageTarget.className = 'text-success mt-1 small';
        } else {
            this.messageTarget.textContent = '✗ Les mots de passe ne correspondent pas';
            this.messageTarget.className = 'text-danger mt-1 small';
        }
    }
}
