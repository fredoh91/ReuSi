import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        intensity: { type: Number, default: 15 } // Intensité maximale du décalage en pixels
    }

    connect() {
        this.lastScrollY = window.scrollY;
        this.onScroll = this.onScroll.bind(this);
        window.addEventListener('scroll', this.onScroll, { passive: true });
    }

    disconnect() {
        window.removeEventListener('scroll', this.onScroll);
    }

    onScroll() {
        const currentScrollY = window.scrollY;
        const delta = currentScrollY - this.lastScrollY;
        
        // Calcul du décalage basé sur la vitesse du scroll (limité par l'intensité)
        // On utilise un multiplicateur (0.4) pour ajuster la réactivité
        const shift = Math.max(-this.intensityValue, Math.min(this.intensityValue, delta * 0.4));
        
        // On applique le décalage vertical tout en conservant le centrage horizontal (translateX(-50%))
        // car la barre est centrée via left: 50% dans le CSS
        this.element.style.transform = `translateX(-50%) translateY(${shift}px)`;
        
        // On remet doucement la barre à sa position initiale après une courte pause dans le scroll
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            this.element.style.transform = `translateX(-50%) translateY(0)`;
        }, 50);

        this.lastScrollY = currentScrollY;
    }
}
