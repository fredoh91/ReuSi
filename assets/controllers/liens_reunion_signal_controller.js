// assets/controllers/liens_reunion_signal_controller.js

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["list", "item"];
    static values = {
        widgetCounter: { type: Number, default: 0 }
    }

    connect() {
        this.widgetCounterValue = this.listTarget.dataset.liensReunionSignalWidgetCounterValue || this.itemTargets.length;
        // Pour les éléments existants, s'assurer que les boutons de suppression sont gérés par Stimulus
        this.itemTargets.forEach(item => {
            if (!item.querySelector('[data-action="liens-reunion-signal#remove"]')) {
                this.addDeleteButton(item);
            }
        });
    }

    add(event) {
        event.preventDefault();

        const prototype = this.listTarget.dataset.prototype;
        const newItemHtml = prototype.replace(/__name__/g, this.widgetCounterValue);

        const item = document.createElement('div');
        // Modification ici : mb-1 p-1
        item.classList.add('lien-item', 'd-flex', 'align-items-center', 'mb-1', 'p-1', 'border', 'rounded');
        item.setAttribute('data-liens-reunion-signal-target', 'item'); // Ajout de l'attribut target
        item.innerHTML = newItemHtml;

        this.listTarget.appendChild(item);
        this.widgetCounterValue++;

        this.addDeleteButton(item); // S'assure que le nouveau bouton a l'action Stimulus
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('[data-liens-reunion-signal-target="item"]').remove();
    }

    addDeleteButton(item) {
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        // Ajout de la classe text-nowrap ici
        removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-3', 'text-nowrap'); 
        removeButton.setAttribute('data-action', 'liens-reunion-signal#remove');
        removeButton.innerHTML = '<i class="bi bi-trash"></i> Supprimer';
        item.appendChild(removeButton);
    }
}
