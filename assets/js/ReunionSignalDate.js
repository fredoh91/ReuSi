document.addEventListener('DOMContentLoaded', function () {
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    if (confirmDeleteModal) {
        const confirmDeleteButton = document.getElementById('confirm-delete-button');
        let formToSubmit = null;

        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            // Bouton qui a déclenché la modale
            const button = event.relatedTarget;
            // Récupérer l'ID du formulaire à soumettre depuis l'attribut data-form-id
            const formId = button.getAttribute('data-form-id');
            formToSubmit = document.getElementById(formId);
        });

        confirmDeleteButton.addEventListener('click', function () {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    }
});

