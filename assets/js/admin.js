/**
 * ============================================
 * JAVASCRIPT MODERNE ET PROFESSIONNEL POUR ADMIN
 * ============================================
 */

// Classe principale pour gérer l'administration
class AdminApp {
    constructor() {
        this.init();
    }

    /**
     * Initialisation de l'application
     */
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initSidebar();
            this.initAnimations();
            this.initForms();
            this.initTables();
            this.initTooltips();
            this.initModals();
            this.initNotifications();
            this.initDashboardCards();
            this.initAutoSave();
            this.initKeyboardShortcuts();
            console.log('Admin App initialisé avec succès');
        });
    }

    /**
     * Gestion de la sidebar (responsive)
     */
    initSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('#sidebarToggle');
        
        if (!sidebar) return;

        // Créer l'overlay pour mobile
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        // Toggle mobile sidebar
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                this.updateBodyPadding(sidebar.classList.contains('show'));
            });
        }

        // Fermer la sidebar en cliquant sur l'overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            this.updateBodyPadding(false);
        });

        // Fermer la sidebar en cliquant à l'extérieur sur mobile
        if (window.innerWidth < 992) {
            document.addEventListener('click', (e) => {
                if (sidebar.classList.contains('show') && 
                    !sidebar.contains(e.target) && 
                    !e.target.closest('#sidebarToggle')) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    this.updateBodyPadding(false);
                }
            });
        }

        // Gérer le resize
        window.addEventListener('resize', this.debounce(() => {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                this.updateBodyPadding(false);
            }
        }, 250));

        // Fermer la sidebar avec Échap
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                this.updateBodyPadding(false);
            }
        });

        // Highlight de la page active dans la sidebar
        this.highlightActivePage(sidebar);
    }

    /**
     * Mettre en évidence la page active
     */
    highlightActivePage(sidebar) {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = sidebar.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.includes(currentPage)) {
                link.classList.add('active');
            }
        });
    }

    /**
     * Mettre à jour le padding du body pour la sidebar mobile
     */
    updateBodyPadding(isOpen) {
        if (window.innerWidth < 992 && isOpen) {
            document.body.style.paddingLeft = '280px';
        } else {
            document.body.style.paddingLeft = '';
        }
    }

    /**
     * Initialiser les animations
     */
    initAnimations() {
        // Animation des cartes au scroll
        if (typeof IntersectionObserver !== 'undefined') {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const animatedElements = document.querySelectorAll('.dashboard-card, .card:not(.no-animate)');
            animatedElements.forEach((element) => {
                const rect = element.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                } else {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(30px)';
                    element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    observer.observe(element);
                }
            });
        }
    }

    /**
     * Initialiser les formulaires
     */
    initForms() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            // Validation en temps réel
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });

            // Soumission du formulaire
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showNotification('Veuillez corriger les erreurs dans le formulaire', 'warning');
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.focus();
                    }
                }
            });
        });

        // Auto-save pour les formulaires longs
        this.initAutoSave();
    }

    /**
     * Valider un champ
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Ce champ est requis';
        }
        // Email
        else if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Email invalide';
        }
        // Min length
        else if (field.hasAttribute('minlength')) {
            const minLength = parseInt(field.getAttribute('minlength'));
            if (value.length < minLength) {
                isValid = false;
                errorMessage = `Minimum ${minLength} caractères requis`;
            }
        }
        // Max length
        else if (field.hasAttribute('maxlength')) {
            const maxLength = parseInt(field.getAttribute('maxlength'));
            if (value.length > maxLength) {
                isValid = false;
                errorMessage = `Maximum ${maxLength} caractères`;
            }
        }

        // Appliquer les classes
        if (isValid && value) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            this.hideFieldError(field);
        } else if (!isValid) {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            this.showFieldError(field, errorMessage);
        } else {
            field.classList.remove('is-invalid', 'is-valid');
            this.hideFieldError(field);
        }

        return isValid;
    }

    /**
     * Valider un formulaire complet
     */
    validateForm(form) {
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Afficher l'erreur d'un champ
     */
    showFieldError(field, message) {
        let errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    /**
     * Masquer l'erreur d'un champ
     */
    hideFieldError(field) {
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    /**
     * Vérifier si un email est valide
     */
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * Initialiser les tables
     */
    initTables() {
        // Recherche dans les tables
        const searchInputs = document.querySelectorAll('.table-search');
        searchInputs.forEach(input => {
            input.addEventListener('input', this.debounce((e) => {
                this.filterTable(e.target);
            }, 300));
        });

        // Tri des colonnes
        const sortableHeaders = document.querySelectorAll('.table th[data-sort]');
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(header);
            });
        });
    }

    /**
     * Filtrer une table
     */
    filterTable(searchInput) {
        const searchTerm = searchInput.value.toLowerCase();
        const table = searchInput.closest('.table-responsive')?.querySelector('table') || 
                     searchInput.closest('main')?.querySelector('table');
        
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    /**
     * Trier une table
     */
    sortTable(header) {
        const table = header.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const isAsc = header.dataset.sort === 'asc';

        rows.sort((a, b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();
            const aValue = isNaN(aText) ? aText : parseFloat(aText);
            const bValue = isNaN(bText) ? bText : parseFloat(bText);

            if (isAsc) {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        rows.forEach(row => tbody.appendChild(row));
        header.dataset.sort = isAsc ? 'desc' : 'asc';
    }

    /**
     * Initialiser les tooltips Bootstrap
     */
    initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map((tooltipTriggerEl) => {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    /**
     * Initialiser les modales
     */
    initModals() {
        // Confirmation de suppression
        const deleteLinks = document.querySelectorAll('a[data-confirm-delete]');
        deleteLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const message = link.getAttribute('data-confirm-delete') || 
                               'Êtes-vous sûr de vouloir supprimer cet élément ?';
                if (confirm(message)) {
                    window.location.href = link.href;
                }
            });
        });
    }

    /**
     * Initialiser les notifications
     */
    initNotifications() {
        // Notification depuis l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const type = urlParams.get('type') || 'success';

        if (message) {
            this.showNotification(decodeURIComponent(message), type);
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Auto-hide des alertes
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                } else {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        });
    }

    /**
     * Afficher une notification
     */
    showNotification(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.style.maxWidth = '500px';
        alertDiv.innerHTML = `
            ${this.escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
                    bsAlert.close();
                } else {
                    alertDiv.style.opacity = '0';
                    setTimeout(() => alertDiv.remove(), 300);
                }
            }
        }, 5000);
    }

    /**
     * Fetch JSON helper that automatically sends CSRF header and handles errors
     */
    async fetchJson(url, options = {}) {
        const headers = options.headers || {};
        headers['X-Requested-With'] = 'XMLHttpRequest';
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) headers['X-CSRF-Token'] = meta.getAttribute('content');

        const opts = Object.assign({}, options, { headers });
        try {
            const res = await fetch(url || window.location.href, opts);
            const text = await res.text();
            try {
                const data = JSON.parse(text);
                return { ok: res.ok, status: res.status, data };
            } catch (e) {
                return { ok: res.ok, status: res.status, text };
            }
        } catch (err) {
            this.showNotification('Erreur réseau : ' + err.message, 'danger');
            return { ok: false, status: 0, error: err };
        }
    }

    /**
     * Initialiser les cartes du dashboard
     */
    initDashboardCards() {
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (!this.href || this.href === '#') {
                    e.preventDefault();
                }
            });

            // Animation au survol
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
    }

    /**
     * Auto-save des formulaires
     */
    initAutoSave() {
        const forms = document.querySelectorAll('form[data-autosave]');
        
        forms.forEach(form => {
            const formId = form.getAttribute('data-autosave');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            // Restaurer les données sauvegardées
            const savedData = localStorage.getItem(`form_${formId}`);
            if (savedData) {
                try {
                    const data = JSON.parse(savedData);
                    Object.keys(data).forEach(key => {
                        const field = form.querySelector(`[name="${key}"]`);
                        if (field && !field.value) {
                            field.value = data[key];
                        }
                    });
                } catch (e) {
                    console.error('Erreur lors de la restauration:', e);
                }
            }

            // Sauvegarder automatiquement
            inputs.forEach(input => {
                input.addEventListener('input', this.debounce(() => {
                    const formData = new FormData(form);
                    const data = {};
                    formData.forEach((value, key) => {
                        data[key] = value;
                    });
                    localStorage.setItem(`form_${formId}`, JSON.stringify(data));
                }, 1000));
            });

            // Nettoyer après soumission
            form.addEventListener('submit', () => {
                localStorage.removeItem(`form_${formId}`);
            });
        });
    }

    /**
     * Raccourcis clavier
     */
    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl + K : Recherche
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.table-search, input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Ctrl + S : Sauvegarder le formulaire
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const form = document.querySelector('form');
                if (form) {
                    form.requestSubmit();
                }
            }

            // Échap : Fermer les modales
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                });
            }
        });
    }

    /**
     * Fonction debounce pour optimiser les performances
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Échapper le HTML pour éviter XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialiser l'application
const adminApp = new AdminApp();

// Export pour utilisation externe
window.AdminApp = AdminApp;

