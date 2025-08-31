document.addEventListener('DOMContentLoaded', function () {
    const btnFormatDCI = document.getElementById('BtnFormatDCI');

    if (btnFormatDCI) {
        btnFormatDCI.addEventListener('click', function () {
            if (!confirm("Voulez vous formater les données d'origine produit du même type que DCI ?")) {
                return;
            }
            // copie de la DCI vers le champ dénomination
            const champDCI = document.getElementById('produits_DCI');
            const champDenomination = document.getElementById('produits_Denomination');
            if (champDCI && champDenomination) {
                champDenomination.value = champDCI.value.toUpperCase();
            }
            // effacement des autres champs
            const champs_a_effacer = document.querySelectorAll('.Chp-a-effacer');
            champs_a_effacer.forEach(function (champ) {
                champ.value = '';
            });
        });
    }
});