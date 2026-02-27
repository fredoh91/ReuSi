import { Controller } from '@hotwired/stimulus';

export default class extends Controller {


    // connect() {
    //     console.log("FormatProduitRechercheController connecté");
    // }


    static targets = [
        // Champs sources pour la copie
        "dci",
        // "nomProduit",

        // Champ de destination pour la copie
        "denomination",

        // Champs à vider lors du formatage DCI
        // "dciClearable",

        // Champs à vider lors du formatage Produit
        // "produitClearable"
    ];

    formatDCI(event) {
        event.preventDefault();

        if (!confirm("Voulez-vous formater les données d'origine produit du même type que la DCI ?")) {
            return;
        }

        // Copie de la DCI vers le champ dénomination en majuscules
        if (this.hasDciTarget && this.hasDenominationTarget) {
            this.denominationTarget.value = this.dciTarget.value.toUpperCase();
        }

        // Effacement des autres champs en utilisant les classes CSS
        // this.element est l'élément <form data-controller="...">
        this.element.querySelectorAll('.Chp-a-effacer-dci').forEach((champ) => {
            champ.value = '';
        });

        // // Effacement des autres champs ciblés pour le formatage DCI
        // this.dciClearableTargets.forEach((champ) => {
        //     champ.value = '';
        // });
    }

    formatProduit(event) {
        event.preventDefault();

        if (!confirm("Voulez-vous formater les données d'origine produit du même type que la dénomination ?")) {
            return;
        }

        // Copie du nom du produit vers le champ dénomination en majuscules
        if (this.hasDciTarget && this.hasDenominationTarget) {
            this.dciTarget.value = this.denominationTarget.value.toUpperCase();
        }

        // Effacement des autres champs en utilisant les classes CSS
        // this.element est l'élément <form data-controller="...">
        this.element.querySelectorAll('.Chp-a-effacer-prod').forEach((champ) => {
            champ.value = '';
        });

        // Effacement des autres champs ciblés pour le formatage Produit
        // this.produitClearableTargets.forEach((champ) => {
        //     champ.value = '';
        // });
    }
}