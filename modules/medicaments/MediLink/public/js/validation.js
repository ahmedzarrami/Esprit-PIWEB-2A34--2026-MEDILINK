/**
 * MediLink — Validation JavaScript Custom
 * AUCUNE validation HTML5 — tout est fait ici
 * Validation au blur et au submit
 */

(function () {
    'use strict';

    // ===== FONCTIONS DE VALIDATION =====

    /**
     * Vérifie si une valeur est vide
     * @param {string} value
     * @returns {boolean}
     */
    function isEmpty(value) {
        return value.trim() === '';
    }

    /**
     * Vérifie la longueur minimale
     * @param {string} value
     * @param {number} min
     * @returns {boolean}
     */
    function minLength(value, min) {
        return value.trim().length >= min;
    }

    /**
     * Vérifie la longueur maximale
     * @param {string} value
     * @param {number} max
     * @returns {boolean}
     */
    function maxLength(value, max) {
        return value.trim().length <= max;
    }

    /**
     * Vérifie le format email
     * @param {string} value
     * @returns {boolean}
     */
    function isEmail(value) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value.trim());
    }

    // ===== AFFICHAGE DES ERREURS =====

    /**
     * Affiche une erreur sous un champ
     * @param {HTMLElement} field
     * @param {string} message
     */
    function showError(field, message) {
        // Retirer l'ancienne erreur s'il y en a une
        clearError(field);

        // Ajouter la classe d'erreur au champ
        field.classList.add('error');

        // Créer le span d'erreur
        var errorSpan = document.createElement('span');
        errorSpan.className = 'error-message';
        errorSpan.textContent = message;

        // Insérer après le champ
        field.parentNode.insertBefore(errorSpan, field.nextSibling);
    }

    /**
     * Efface l'erreur d'un champ
     * @param {HTMLElement} field
     */
    function clearError(field) {
        field.classList.remove('error');
        var existingError = field.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * Efface toutes les erreurs d'un formulaire
     * @param {HTMLFormElement} form
     */
    function clearAllErrors(form) {
        var errorFields = form.querySelectorAll('.error');
        for (var i = 0; i < errorFields.length; i++) {
            errorFields[i].classList.remove('error');
        }
        var errorMessages = form.querySelectorAll('.error-message');
        for (var j = 0; j < errorMessages.length; j++) {
            errorMessages[j].remove();
        }
    }

    // ===== VALIDATION D'UN CHAMP =====

    /**
     * Valide un champ selon ses attributs data-*
     * @param {HTMLElement} field
     * @returns {boolean}
     */
    function validateField(field) {
        var value = field.value;
        var fieldName = field.getAttribute('data-label') || 'Ce champ';

        // Champ requis
        if (field.getAttribute('data-required') === 'true') {
            if (isEmpty(value)) {
                showError(field, fieldName + ' est obligatoire.');
                return false;
            }
        }

        // Ne pas valider plus loin si vide et non requis
        if (isEmpty(value)) {
            clearError(field);
            return true;
        }

        // Longueur minimale
        var min = field.getAttribute('data-min');
        if (min && !minLength(value, parseInt(min))) {
            showError(field, fieldName + ' doit contenir au moins ' + min + ' caractères.');
            return false;
        }

        // Longueur maximale
        var max = field.getAttribute('data-max');
        if (max && !maxLength(value, parseInt(max))) {
            showError(field, fieldName + ' ne doit pas dépasser ' + max + ' caractères.');
            return false;
        }

        // Format email
        if (field.getAttribute('data-type') === 'email') {
            if (!isEmail(value)) {
                showError(field, 'Veuillez entrer une adresse email valide.');
                return false;
            }
        }

        // Si tout est OK, effacer l'erreur
        clearError(field);
        return true;
    }

    // ===== INITIALISATION =====

    /**
     * Initialise la validation sur tous les formulaires avec data-validate
     */
    function initValidation() {
        var forms = document.querySelectorAll('form[data-validate="true"]');

        for (var f = 0; f < forms.length; f++) {
            (function (form) {
                // Désactiver la validation HTML5 native
                form.setAttribute('novalidate', 'novalidate');

                // Récupérer tous les champs à valider
                var fields = form.querySelectorAll('[data-required], [data-min], [data-max], [data-type]');

                // Validation au blur (quand on quitte un champ)
                for (var i = 0; i < fields.length; i++) {
                    fields[i].addEventListener('blur', function () {
                        validateField(this);
                    });

                    // Effacer l'erreur quand l'utilisateur tape
                    fields[i].addEventListener('input', function () {
                        if (this.classList.contains('error')) {
                            validateField(this);
                        }
                        // Compteur de caractères
                        updateCharCounter(this);
                    });
                }

                // Validation au submit
                form.addEventListener('submit', function (e) {
                    clearAllErrors(form);

                    var isValid = true;
                    var firstError = null;

                    for (var j = 0; j < fields.length; j++) {
                        if (!validateField(fields[j])) {
                            isValid = false;
                            if (!firstError) {
                                firstError = fields[j];
                            }
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                        // Focus sur le premier champ en erreur
                        if (firstError) {
                            firstError.focus();
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                });
            })(forms[f]);
        }
    }

    // ===== COMPTEUR DE CARACTÈRES =====

    /**
     * Met à jour le compteur de caractères
     * @param {HTMLElement} field
     */
    function updateCharCounter(field) {
        var max = field.getAttribute('data-max');
        if (!max) return;

        var counterEl = field.parentNode.querySelector('.char-counter');
        if (!counterEl) {
            counterEl = document.createElement('div');
            counterEl.className = 'char-counter';
            field.parentNode.appendChild(counterEl);
        }

        var remaining = parseInt(max) - field.value.length;
        counterEl.textContent = field.value.length + ' / ' + max + ' caractères';

        if (remaining < 0) {
            counterEl.style.color = '#ef4444';
        } else if (remaining < 50) {
            counterEl.style.color = '#f97316';
        } else {
            counterEl.style.color = '';
        }
    }

    // ===== MODAL DE CONFIRMATION =====

    /**
     * Ouvre une modale de confirmation de suppression
     * @param {string} deleteUrl - URL de suppression
     * @param {string} itemName - Nom de l'élément à supprimer
     */
    window.confirmDelete = function (deleteUrl, itemName) {
        // Chercher la modale existante ou en créer une
        var overlay = document.getElementById('deleteModal');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'deleteModal';
            overlay.className = 'modal-overlay admin-modal-overlay';
            overlay.innerHTML =
                '<div class="modal-content admin-modal">' +
                    '<div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>' +
                    '<h3>Confirmer la suppression</h3>' +
                    '<p id="deleteModalMessage"></p>' +
                    '<div class="modal-actions">' +
                        '<button class="btn btn-secondary admin-btn admin-btn-secondary" onclick="closeDeleteModal()">Annuler</button>' +
                        '<a id="deleteModalLink" class="btn btn-danger admin-btn admin-btn-danger" href="#">Supprimer</a>' +
                    '</div>' +
                '</div>';
            document.body.appendChild(overlay);
        }

        document.getElementById('deleteModalMessage').textContent =
            'Êtes-vous sûr de vouloir supprimer ' + (itemName || 'cet élément') + ' ? Cette action est irréversible.';
        document.getElementById('deleteModalLink').href = deleteUrl;

        overlay.classList.add('active');
    };

    /**
     * Ferme la modale de suppression
     */
    window.closeDeleteModal = function () {
        var overlay = document.getElementById('deleteModal');
        if (overlay) {
            overlay.classList.remove('active');
        }
    };

    // Fermer la modale en cliquant sur l'overlay
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('admin-modal-overlay')) {
            e.target.classList.remove('active');
        }
    });

    // ===== LANCEMENT =====
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initValidation);
    } else {
        initValidation();
    }

})();
