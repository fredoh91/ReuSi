import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String,
        token: String,
        confirmMessage: String,
        fileName: String // Ajout de fileName
    }

    connect() {
        // console.log('fichiers_attaches_reunion_signal_controller connecté !', this.element);
        // console.log('URL de suppression :', this.urlValue);
        // console.log('Message de confirmation (au connect) :', this.confirmMessageValue);
        // console.log('Nom du fichier (au connect) :', this.fileNameValue); // Log du nom du fichier
    }

    async delete(event) {
        event.preventDefault();
        // console.log('Méthode delete déclenchée !');

        // Construire le message de confirmation
        let fullConfirmMessage = this.confirmMessageValue;
        if (this.fileNameValue) {

            // console.log('Nom du fichier à supprimer :', this.fileNameValue);    

            const truncatedFileName = this.fileNameValue.length > 50 
                                      ? this.fileNameValue.substring(0, 47) + '...' 
                                      : this.fileNameValue;
            fullConfirmMessage += "\n\n" + truncatedFileName; // Nouvelle ligne pour le nom du fichier
        }

        // console.log('Valeur du message de confirmation complet avant confirm() :', fullConfirmMessage);

        if (!confirm(fullConfirmMessage)) {
            // console.log('Suppression annulée par l\'utilisateur.');
            return;
        }

        // console.log('Confirmation acceptée. Envoi de la requête de suppression...');
        try {
            const response = await fetch(this.urlValue, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    '_token': this.tokenValue
                })
            });

            // console.log('Requête fetch envoyée. Réponse reçue :', response);

            if (response.ok) {
                // console.log('Suppression réussie côté serveur. Suppression de l\'élément du DOM.');
                this.element.remove();
            } else {
                console.error('La suppression a échoué côté serveur. Statut :', response.status);
                const errorData = await response.json().catch(() => ({ message: 'Erreur inattendue.' }));
                alert(`Erreur: ${errorData.message || 'La suppression a échoué.'}`);
                console.error('Détails de l\'erreur :', errorData);
            }
        } catch (error) {
            console.error('Erreur lors de la requête de suppression (réseau ou autre) :', error);
            alert('Une erreur réseau est survenue.');
        }
    }
}
