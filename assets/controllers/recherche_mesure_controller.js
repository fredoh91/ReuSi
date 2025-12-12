// assets/controllers/recherche_mesure_controller.js
import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css'; // Importez le CSS de Tom-Select pour Bootstrap 5

/*
 * Ce contrôleur transforme un champ <select> en un champ de recherche
 * riche en fonctionnalités en utilisant la bibliothèque Tom-Select.
 *
 * Utilisation dans Twig :
 * <div {{ stimulus_controller('recherche-mesure') }}>
 *     {{ form_widget(form.LibMesure) }}
 * </div>
 */
export default class extends Controller {
    connect() {
        const selectElement = this.element.querySelector('select');
        if (selectElement) {
            new TomSelect(selectElement, {
                create: false, // Empêche l'ajout de nouvelles options
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                // Vous pouvez ajouter d'autres options de Tom-Select ici si nécessaire
                // Par exemple, pour les traductions si Tom-Select le supporte ou d'autres comportements
            });
        } else {
            console.error("Aucun élément <select> trouvé dans le contrôleur recherche-mesure.");
        }
    }
}
