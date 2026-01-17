/**
 * ============================================
 * JAVASCRIPT COMPLET POUR AJOUTER DOMAINE
 * ============================================
 */

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    initDomaineForm();
    initAutoHideAlert();
    initInputValidation();
    initFormSubmission();
    initSearchFilter();
});

/**
 * Initialisation du formulaire
 */
function initDomaineForm() {
    const form = document.getElementById('formDomaine');
    const inputLibelle = document.getElementById('libelle');
    const btnSubmit = document.getElementById('btnSubmit');

    if (!form || !inputLibelle) return;

    // Focus automatique sur l'input
    inputLibelle.focus();

    // Vérification en temps réel
    inputLibelle.addEventListener('input', function() {
        validateLibelle(this);
    });

    // Validation au blur
    inputLibelle.addEventListener('blur', function() {
        validateLibelle(this);
    });
}

/**
 * Validation du champ libellé
 */
function validateLibelle(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('libelleError');
    let isValid = true;
    let errorMessage = '';

    // Supprimer les classes précédentes
    input.classList.remove('is-valid', 'is-invalid');

    // Validation : vide
    if (value === '') {
        isValid = false;
        errorMessage = 'Le libellé est requis';
    }
    // Validation : longueur minimale
    else if (value.length < 2) {
        isValid = false;
        errorMessage = 'Le libellé doit contenir au moins 2 caractères';
    }
    // Validation : longueur maximale
    else if (value.length > 50) {
        isValid = false;
        errorMessage = 'Le libellé ne doit pas dépasser 50 caractères';
    }
    // Validation : caractères spéciaux (optionnel - seulement lettres, espaces, tirets)
    else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)) {
        // Permettre lettres, espaces, tirets et apostrophes
        isValid = false;
        errorMessage = 'Le libellé ne doit contenir que des lettres, espaces, tirets et apostrophes';
    }

    // Appliquer les classes et messages
    if (isValid && value !== '') {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
        errorDiv.textContent = '';
    } else if (!isValid) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        errorDiv.textContent = errorMessage;
    }

    return isValid;
}

/**
 * Validation des inputs en temps réel
 */
function initInputValidation() {
    const inputLibelle = document.getElementById('libelle');
    
    if (inputLibelle) {
        // Empêcher la saisie de caractères non autorisés
        inputLibelle.addEventListener('keypress', function(e) {
            // Autoriser lettres, espaces, tirets, apostrophes
            const char = String.fromCharCode(e.which);
            if (!/^[a-zA-ZÀ-ÿ\s\-']$/.test(char) && e.which !== 8 && e.which !== 0) {
                e.preventDefault();
                showTemporaryMessage('Caractère non autorisé', 'warning');
            }
        });

        // Capitaliser la première lettre
        inputLibelle.addEventListener('blur', function() {
            const value = this.value.trim();
            if (value.length > 0) {
                this.value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
            }
        });
    }
}

/**
 * Gestion de la soumission du formulaire
 */
function initFormSubmission() {
    const form = document.getElementById('formDomaine');
    const btnSubmit = document.getElementById('btnSubmit');
    const inputLibelle = document.getElementById('libelle');

    if (!form || !btnSubmit) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validation avant soumission
        const isValid = validateLibelle(inputLibelle);

        if (!isValid) {
            showTemporaryMessage('Veuillez corriger les erreurs dans le formulaire', 'danger');
            inputLibelle.focus();
            return;
        }

        // Désactiver le bouton et afficher le loading
        btnSubmit.disabled = true;
        btnSubmit.classList.add('loading');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="bi bi-hourglass-split"></i> Ajout en cours...';

        // Soumettre via AJAX pour une UX fluide
        const formData = new FormData(form);

        (async function() {
            const res = await window.appFetch.fetchJson(window.location.href, { method: 'POST', body: formData });
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('loading');
            btnSubmit.innerHTML = originalText;

            if (!res.ok) {
                const msg = (res.data && res.data.errors) ? res.data.errors.join(' / ') : (res.text || 'Erreur lors de la requête');
                showTemporaryMessage(msg, 'danger');
                return;
            }

            // Réponse JSON attendue
            if (res.data && res.data.status === 'ok') {
                showTemporaryMessage('Domaine ajouté avec succès', 'success');
                form.reset();
                // Anim et insertion dans la liste
                if (typeof animateNewItem === 'function') {
                    animateNewItem(res.data.name, res.data.id);
                }
            } else {
                const msg = (res.data && res.data.errors) ? res.data.errors.join(' / ') : 'Erreur inattendue';
                showTemporaryMessage(msg, 'danger');
            }
        })();
    });
}

/**
 * Masquer automatiquement les alertes après quelques secondes
 */
function initAutoHideAlert() {
    const alert = document.getElementById('alertMessage');
    
    if (alert) {
        // Masquer après 5 secondes pour les alertes de succès
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
        // Masquer après 7 secondes pour les alertes d'erreur/avertissement
        else if (alert.classList.contains('alert-danger') || alert.classList.contains('alert-warning')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 7000);
        }
    }
}

/**
 * Filtre de recherche pour la liste des domaines
 */
function initSearchFilter() {
    const listeDomaines = document.getElementById('listeDomaines');
    
    if (!listeDomaines) return;

    // Créer un champ de recherche si la liste existe
    const cardHeader = listeDomaines.closest('.card').querySelector('.card-header');
    if (cardHeader && !document.getElementById('searchInput')) {
        const searchContainer = document.createElement('div');
        searchContainer.className = 'px-3 pb-2';
        searchContainer.innerHTML = `
            <input type="text" 
                   id="searchInput" 
                   class="form-control form-control-sm" 
                   placeholder="Rechercher un domaine...">
        `;
        cardHeader.parentNode.insertBefore(searchContainer, cardHeader.nextSibling);

        const searchInput = document.getElementById('searchInput');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const items = listeDomaines.querySelectorAll('.list-group-item');
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Afficher un message si aucun résultat
            let noResultsMsg = document.getElementById('noResultsMsg');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMsg';
                    noResultsMsg.className = 'text-center text-muted py-3';
                    noResultsMsg.textContent = 'Aucun domaine trouvé';
                    listeDomaines.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
    }
}

/**
 * Afficher un message temporaire
 */
function showTemporaryMessage(message, type = 'info') {
    // Also show site-wide toast notification
    if (window.appFetch && typeof window.appFetch.notify === 'function') {
        const mapped = (type === 'warning') ? 'info' : type; // bootstrap warning -> info toast
        window.appFetch.notify(message, mapped);
    }

    // Keep inline form alert for screen-reader visibility
    // Supprimer les messages existants
    const existingMsg = document.querySelector('.temp-message');
    if (existingMsg) {
        existingMsg.remove();
    }

    // Créer le nouveau message
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show temp-message`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    `;

    // Insérer avant le formulaire
    const form = document.getElementById('formDomaine');
    if (form) {
        form.parentNode.insertBefore(alertDiv, form);
        
        // Masquer automatiquement après 3 secondes
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 3000);
    }
}

/**
 * Confirmation avant soumission (optionnel)
 */
function confirmBeforeSubmit() {
    const inputLibelle = document.getElementById('libelle');
    const value = inputLibelle.value.trim();
    
    return confirm(`Êtes-vous sûr de vouloir ajouter le domaine "${value}" ?`);
}

/**
 * Raccourcis clavier
 */
document.addEventListener('keydown', function(e) {
    // Ctrl + Enter pour soumettre le formulaire
    if (e.ctrlKey && e.key === 'Enter') {
        const form = document.getElementById('formDomaine');
        if (form) {
            form.dispatchEvent(new Event('submit'));
        }
    }

    // Échap pour effacer le champ
    if (e.key === 'Escape') {
        const inputLibelle = document.getElementById('libelle');
        if (inputLibelle && document.activeElement === inputLibelle) {
            inputLibelle.value = '';
            inputLibelle.classList.remove('is-valid', 'is-invalid');
        }
    }
});

/**
 * Animation d'ajout dans la liste (après soumission réussie)
 */
function animateNewItem(domaineName, domaineId) {
    const listeDomaines = document.getElementById('listeDomaines');
    
    if (!listeDomaines) return;

    const newItem = document.createElement('div');
    newItem.className = 'list-group-item d-flex justify-content-between align-items-center';
    newItem.style.opacity = '0';
    newItem.style.transform = 'translateX(-20px)';
    newItem.innerHTML = `
        <span>
            <i class="bi bi-tag-fill text-primary"></i>
            ${domaineName}
        </span>
        <span class="badge bg-secondary rounded-pill">#${domaineId}</span>
    `;

    listeDomaines.insertBefore(newItem, listeDomaines.firstChild);

    // Animation
    setTimeout(() => {
        newItem.style.transition = 'all 0.3s ease';
        newItem.style.opacity = '1';
        newItem.style.transform = 'translateX(0)';
    }, 100);
}

/**
 * Export des fonctions pour utilisation externe
 */
window.domaineForm = {
    validate: validateLibelle,
    showMessage: showTemporaryMessage,
    animateNewItem: animateNewItem
};

