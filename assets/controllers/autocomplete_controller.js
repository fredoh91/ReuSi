// assets/controllers/autocomplete_controller.js
import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

/**
 * Contrôleur pour transformer un champ texte en champ d'autocomplétion.
 *
 * Utilisation :
 * <input type="text" data-controller="autocomplete">
 */
export default class extends Controller {
    connect() {
        const options = [
            {value: 'CRPV/CM', text: 'CRPV/CM'},
            {value: 'CRPV', text: 'CRPV'},
            {value: 'CEIP/SIMAD', text: 'CEIP/SIMAD'},
            {value: 'CEIP', text: 'CEIP'},
            {value: 'EMM', text: 'EMM'},
            {value: 'CASAR', text: 'CASAR'},
            {value: 'suivi nat', text: 'suivi nat'},
        ];

        new TomSelect(this.element, {
            // Permet à l'utilisateur de créer une nouvelle entrée qui n'est pas dans la liste
            create: true,
            // On s'assure qu'un seul élément peut être sélectionné
            maxItems: 1,
            options: options,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    }
}