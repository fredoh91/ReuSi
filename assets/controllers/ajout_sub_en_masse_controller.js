import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["medCheckbox", "nonMedCheckbox", "spinner"];
    static values = {
        signalId: Number,
        url: String,
    };

    async submit(event) {
        event.preventDefault();
        const button = event.currentTarget;

        const selectedMeds = this.medCheckboxTargets
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        const selectedNonMeds = this.nonMedCheckboxTargets
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedMeds.length === 0 && selectedNonMeds.length === 0) {
            alert('Veuillez sélectionner au moins un médicament ou une substance.');
            return;
        }

        // Affiche le spinner et désactive le bouton
        this.spinnerTarget.classList.remove('d-none');
        button.disabled = true;

        // console.log('Stimulus: Tentative d\'ajout en masse...', {
        //     url: this.urlValue,
        //     signalId: this.signalIdValue,
        //     meds: selectedMeds,
        //     nonMeds: selectedNonMeds
        // });

        try {
            const response = await fetch(this.urlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    meds: selectedMeds,
                    nonMeds: selectedNonMeds
                })
            });

            // console.log('Stimulus: Réponse reçue:', response.status, response.statusText);

            if (!response.ok) {
                // const errorBody = await response.text();
                // console.error('Stimulus: Erreur serveur (corps):', errorBody);
                throw new Error('Une erreur est survenue lors de l\'ajout.');
            }
            
            const result = await response.json();
            // console.log('Stimulus: Résultat JSON:', result);

            // Redirection vers la page de modification du signal
            if (result.redirectTo) {
                // console.log('Stimulus: Redirection vers:', result.redirectTo);
                window.location.href = result.redirectTo;
            }

        } catch (error) {
            // console.error('Erreur:', error);
            alert(error.message);
            // Réactive le bouton en cas d'erreur
            this.spinnerTarget.classList.add('d-none');
            button.disabled = false;
        }
    }
}
