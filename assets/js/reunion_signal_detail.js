// assets/js/reunion_signal_detail.js
import { Tab } from 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const reunionTab = document.getElementById('reunionTab');
    if (!reunionTab) {
        return;
    }

    // --- 1. Gérer l'activation de l'onglet et le scroll au chargement ---
    function activateTabFromHash() {
        let hash = window.location.hash;
        if (!hash) {
            return;
        }

        // Sépare l'ID de l'onglet de l'ID de l'élément (ex: #suivis-signaux:suivi-123)
        const parts = hash.substring(1).split(':');
        const tabId = parts[0];
        const elementId = parts.length > 1 ? parts[1] : null;

        const tabToActivate = document.querySelector(`#reunionTab button[data-bs-target="#${tabId}"]`);

        if (tabToActivate) {
            const tab = Tab.getOrCreateInstance(tabToActivate);

            const scrollToAndHighlight = () => {
                if (elementId) {
                    const targetElement = document.getElementById(elementId);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        targetElement.classList.add('highlight');
                        setTimeout(() => {
                            targetElement.classList.remove('highlight');
                        }, 2500);
                    }
                }
            };

            // Si l'onglet cible n'est pas déjà l'onglet actif, on l'active
            // et on attend la fin de la transition pour scroller.
            if (!tabToActivate.classList.contains('active')) {
                tabToActivate.addEventListener('shown.bs.tab', scrollToAndHighlight, { once: true });
                tab.show();
            } else {
                // Si l'onglet est déjà actif, on peut scroller immédiatement.
                scrollToAndHighlight();
            }
        }
    }

    // --- 2. Mettre à jour l'URL lors du changement d'onglet ---
    const tabButtons = document.querySelectorAll('#reunionTab button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (event) {
            const newHash = event.target.getAttribute('data-bs-target');
            // Utilise history.pushState pour changer l'URL sans recharger et sans sauter à l'ancre
            history.pushState(null, null, newHash);
        });
    });

    // Activer l'onglet au chargement initial
    activateTabFromHash();

    // Gérer les clics sur les boutons "précédent/suivant" du navigateur
    window.addEventListener('popstate', activateTabFromHash);
});
