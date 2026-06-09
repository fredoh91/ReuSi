import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'item', 'container'];

    connect() {
        console.log('RechercheSignalReunionController : Connecté !');
        console.log('Nombre d\'items surveillés :', this.itemTargets.length);
    }

    filter() {
        const query = this.inputTarget.value.toLowerCase().trim();
        console.log('Filtrage en cours pour :', query);

        this.itemTargets.forEach(item => {
            const searchableText = item.dataset.searchContent.toLowerCase();
            if (searchableText.includes(query)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });

        this.updateContainers(query);
    }

    clear() {
        this.inputTarget.value = '';
        this.filter();
    }

    updateContainers(query) {
        if (this.hasContainerTarget) {
            this.containerTargets.forEach(container => {
                const visibleItems = container.querySelectorAll('[data-recherche-signal-reunion-target="item"]:not(.d-none)');
                if (visibleItems.length === 0 && query !== '') {
                    container.classList.add('d-none');
                } else {
                    container.classList.remove('d-none');
                }
            });
        }
    }
}
