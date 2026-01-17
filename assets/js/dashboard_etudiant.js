/**
 * ============================================
 * JAVASCRIPT COMPLET POUR DASHBOARD ÉTUDIANT
 * ============================================
 */

// Marquer que JavaScript est activé
document.documentElement.classList.add('js-enabled');

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
    initAnimations();
    initTooltips();
    initAutoRefresh();
    initNotifications();
});

/**
 * Initialisation du dashboard
 */
function initDashboard() {
    // Ajouter des effets de survol aux cartes (seulement si pas déjà géré par CSS)
    const cards = document.querySelectorAll('.card, .stat-card');
    cards.forEach(card => {
        // Ne pas ajouter d'événements si le CSS gère déjà le hover
        if (!card.classList.contains('no-hover-effect')) {
            card.addEventListener('mouseenter', function() {
                if (!this.style.transform || this.style.transform === 'translateY(0)') {
                    this.style.transform = 'translateY(-5px)';
                }
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
    });

    // Animation des statistiques au chargement (avec délai pour laisser le temps au CSS)
    setTimeout(() => {
        animateStats();
    }, 100);
}

/**
 * Animation des statistiques
 */
function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const finalValue = stat.textContent.trim();
        
        // Vérifier si c'est un nombre (exclure "N/A" et les valeurs avec "/")
        if (finalValue && !isNaN(finalValue) && finalValue !== 'N/A' && !finalValue.includes('/')) {
            const target = parseInt(finalValue);
            if (target > 0) {
                const originalValue = stat.textContent;
                let current = 0;
                const increment = target / 30;
                const duration = 1000; // 1 seconde
                const stepTime = duration / 30;
                
                stat.textContent = '0';
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, stepTime);
            }
        }
    });
}

/**
 * Initialisation des animations
 */
function initAnimations() {
    // Vérifier si IntersectionObserver est supporté
    if (typeof IntersectionObserver === 'undefined') {
        // Fallback : afficher directement les éléments
        document.querySelectorAll('.card, .stat-card').forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
        return;
    }

    // Observer pour les animations au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                // Ne plus observer cet élément
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer les cartes
    document.querySelectorAll('.card, .stat-card').forEach(card => {
        // Ne pas animer si déjà visible
        const rect = card.getBoundingClientRect();
        if (rect.top < window.innerHeight) {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        }
    });
}

/**
 * Initialisation des tooltips Bootstrap
 */
function initTooltips() {
    // Attendre que Bootstrap soit chargé
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        try {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                try {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                } catch (e) {
                    console.warn('Erreur lors de l\'initialisation du tooltip:', e);
                }
            });
        } catch (e) {
            console.warn('Erreur lors de l\'initialisation des tooltips:', e);
        }
    }
}

/**
 * Auto-refresh des données (optionnel)
 */
function initAutoRefresh() {
    // Rafraîchir les statistiques toutes les 5 minutes
    const refreshInterval = 5 * 60 * 1000; // 5 minutes
    
    setInterval(() => {
        // Optionnel : recharger la page ou faire une requête AJAX
        // window.location.reload();
        console.log('Données rafraîchies');
    }, refreshInterval);
}

/**
 * Gestion des notifications
 */
function initNotifications() {
    // Vérifier s'il y a des messages dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const type = urlParams.get('type') || 'success';
    
    if (message) {
        showNotification(message, type);
        // Nettoyer l'URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

/**
 * Afficher une notification
 */
function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.maxWidth = '500px';
    alertDiv.innerHTML = `
        ${escapeHtml(message)}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Masquer automatiquement après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                try {
                    const bsAlert = new bootstrap.Alert(alertDiv);
                    bsAlert.close();
                } catch (e) {
                    // Fallback si Bootstrap n'est pas disponible
                    alertDiv.style.opacity = '0';
                    setTimeout(() => alertDiv.remove(), 300);
                }
            } else {
                // Fallback si Bootstrap n'est pas disponible
                alertDiv.style.opacity = '0';
                alertDiv.style.transition = 'opacity 0.3s';
                setTimeout(() => alertDiv.remove(), 300);
            }
        }
    }, 5000);
}

/**
 * Échapper le HTML pour éviter les injections XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Confirmation avant action
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Formatage des dates
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('fr-FR', options);
}

/**
 * Copier dans le presse-papier
 */
function copyToClipboard(text) {
    // Vérifier si l'API Clipboard est disponible
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copié dans le presse-papier', 'success');
        }).catch(err => {
            console.error('Erreur lors de la copie:', err);
            // Fallback pour les navigateurs plus anciens
            fallbackCopyToClipboard(text);
        });
    } else {
        // Fallback pour les navigateurs plus anciens
        fallbackCopyToClipboard(text);
    }
}

/**
 * Méthode de fallback pour copier dans le presse-papier
 */
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showNotification('Copié dans le presse-papier', 'success');
        } else {
            showNotification('Impossible de copier dans le presse-papier', 'warning');
        }
    } catch (err) {
        console.error('Erreur lors de la copie (fallback):', err);
        showNotification('Erreur lors de la copie', 'danger');
    } finally {
        document.body.removeChild(textArea);
    }
}

/**
 * Raccourcis clavier
 */
document.addEventListener('keydown', function(e) {
    // Ctrl + R pour rafraîchir (désactivé pour éviter les rechargements accidentels)
    if (e.ctrlKey && e.key === 'r') {
        // Optionnel : empêcher le rechargement
        // e.preventDefault();
    }
    
    // Échap pour fermer les modales
    if (e.key === 'Escape') {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                try {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                } catch (e) {
                    console.warn('Erreur lors de la fermeture de la modale:', e);
                }
            });
        }
    }
});

/**
 * Gestion du mode sombre (optionnel)
 */
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Charger le mode sombre si sauvegardé
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}

/**
 * Export des statistiques (optionnel)
 */
function exportStats() {
    const stats = {
        totalStages: document.querySelector('.stat-primary .stat-number')?.textContent,
        stageAffecte: document.querySelector('.stat-success .stat-number')?.textContent,
        notesEnregistrees: document.querySelector('.stat-warning .stat-number')?.textContent,
        moyenne: document.querySelector('.stat-info .stat-number')?.textContent,
        date: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(stats, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'statistiques-stage.json';
    link.click();
}

/**
 * Fonction pour mettre à jour les statistiques en temps réel (AJAX)
 */
function refreshStats() {
    // Vérifier si fetch est disponible
    if (typeof fetch === 'undefined') {
        console.warn('fetch n\'est pas disponible dans ce navigateur');
        return;
    }

    // Exemple de requête AJAX (à adapter selon votre API)
    fetch('api/stats.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            updateStatsDisplay(data);
        })
        .catch(error => {
            console.error('Erreur lors du rafraîchissement:', error);
        });
}

/**
 * Mettre à jour l'affichage des statistiques
 */
function updateStatsDisplay(data) {
    if (data.totalStages !== undefined) {
        const el = document.querySelector('.stat-primary .stat-number');
        if (el) animateToValue(el, data.totalStages);
    }
    if (data.stageAffecte !== undefined) {
        const el = document.querySelector('.stat-success .stat-number');
        if (el) animateToValue(el, data.stageAffecte);
    }
    // ... autres statistiques
}

/**
 * Animer vers une valeur
 */
function animateToValue(element, targetValue) {
    const current = parseInt(element.textContent) || 0;
    const target = parseInt(targetValue);
    const increment = (target - current) / 30;
    const stepTime = 1000 / 30;
    
    let currentValue = current;
    const timer = setInterval(() => {
        currentValue += increment;
        if ((increment > 0 && currentValue >= target) || (increment < 0 && currentValue <= target)) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(currentValue);
        }
    }, stepTime);
}

/**
 * Export des fonctions pour utilisation externe
 */
window.dashboardEtudiant = {
    showNotification: showNotification,
    confirmAction: confirmAction,
    formatDate: formatDate,
    copyToClipboard: copyToClipboard,
    toggleDarkMode: toggleDarkMode,
    exportStats: exportStats,
    refreshStats: refreshStats
};

// Log de chargement
console.log('Dashboard étudiant initialisé avec succès');

