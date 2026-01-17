/**
 * ============================================
 * JAVASCRIPT POUR PAGE D'ACCUEIL
 * ============================================
 */

// Marquer que JavaScript est activé
document.documentElement.classList.add('js-enabled');

document.addEventListener('DOMContentLoaded', function() {
    initAnimations();
    initStatsCounter();
    initSmoothScroll();
    initNavbarScroll();
    initFloatingCards();
    initLoginRedirect();
});

/**
 * Animation des statistiques au scroll
 */
function initStatsCounter() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    if (statNumbers.length === 0) return;
    
    // Vérifier si IntersectionObserver est supporté
    if (typeof IntersectionObserver === 'undefined') {
        // Fallback : animer directement
        statNumbers.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target')) || 0;
            if (target > 0) {
                animateCounter(stat, target);
            }
        });
        return;
    }
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const targetAttr = entry.target.getAttribute('data-target');
                if (targetAttr) {
                    const target = parseInt(targetAttr);
                    if (!isNaN(target) && target >= 0) {
                        animateCounter(entry.target, target);
                        observer.unobserve(entry.target);
                    }
                }
            }
        });
    }, observerOptions);

    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
}

/**
 * Animer un compteur
 */
function animateCounter(element, target) {
    if (!element || isNaN(target) || target < 0) return;
    
    // Sauvegarder la valeur originale si déjà animée
    if (element.dataset.animating === 'true') return;
    element.dataset.animating = 'true';
    
    let current = 0;
    const steps = 50;
    const increment = target / steps;
    const duration = 2000; // 2 secondes
    const stepTime = duration / steps;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
            element.dataset.animating = 'false';
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepTime);
}

/**
 * Animations au scroll
 */
function initAnimations() {
    const elements = document.querySelectorAll('.stat-box, .stage-card, .login-card');
    
    if (elements.length === 0) return;
    
    // Vérifier si IntersectionObserver est supporté
    if (typeof IntersectionObserver === 'undefined') {
        // Fallback : afficher directement
        elements.forEach(element => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        });
        return;
    }
    
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

    elements.forEach(element => {
        // Ne pas animer si déjà visible
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

/**
 * Smooth scroll pour les liens d'ancrage
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const navbar = document.querySelector('.navbar');
                    const navbarHeight = navbar ? navbar.offsetHeight : 76;
                    const offsetTop = target.offsetTop - navbarHeight;
                    
                    // Vérifier si smooth scroll est supporté
                    if ('scrollBehavior' in document.documentElement.style) {
                        window.scrollTo({
                            top: Math.max(0, offsetTop),
                            behavior: 'smooth'
                        });
                    } else {
                        // Fallback pour navigateurs plus anciens
                        window.scrollTo(0, Math.max(0, offsetTop));
                    }
                    
                    // Fermer le menu mobile si ouvert
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                            try {
                                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                                if (bsCollapse) {
                                    bsCollapse.hide();
                                }
                            } catch (e) {
                                // Fallback si Bootstrap n'est pas disponible
                                navbarCollapse.classList.remove('show');
                            }
                        } else {
                            navbarCollapse.classList.remove('show');
                        }
                    }
                }
            }
        });
    });
}

/**
 * Navbar qui change au scroll
 */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    
    let ticking = false;

    function updateNavbar() {
        const currentScroll = window.pageYOffset || window.scrollY || document.documentElement.scrollTop;

        if (currentScroll > 100) {
            navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            navbar.style.padding = '0.5rem 0';
            navbar.classList.add('scrolled');
        } else {
            navbar.style.boxShadow = 'none';
            navbar.style.padding = '1rem 0';
            navbar.classList.remove('scrolled');
        }
        
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    }, { passive: true });
}

/**
 * Animation des cartes flottantes
 */
function initFloatingCards() {
    const cards = document.querySelectorAll('.floating-card');
    
    if (cards.length === 0) return;
    
    // Ne pas interférer avec l'animation CSS float
    // Ajouter seulement une légère variation
    cards.forEach((card, index) => {
        let intervalId;
        
        // Démarrer l'animation après un délai
        setTimeout(() => {
            intervalId = setInterval(() => {
                // Ajouter une légère variation sans remplacer l'animation CSS
                const randomY = Math.random() * 5 - 2.5;
                const randomX = Math.random() * 3 - 1.5;
                
                // Préserver l'animation CSS float et ajouter la variation
                const currentTransform = window.getComputedStyle(card).transform;
                if (currentTransform === 'none') {
                    card.style.transform = `translate(${randomX}px, ${randomY}px)`;
                } else {
                    // Ne pas interférer si l'animation CSS est active
                }
            }, 3000 + index * 1000);
        }, 1000);
        
        // Nettoyer l'intervalle si la carte est retirée du DOM
        const observer = new MutationObserver(() => {
            if (!document.body.contains(card)) {
                clearInterval(intervalId);
                observer.disconnect();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
}

/**
 * Gestion du formulaire de recherche (si ajouté plus tard)
 */
function initSearch() {
    const searchInput = document.querySelector('#searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.stage-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Gestion de la redirection après connexion
 */
function initLoginRedirect() {
    const loginSuccessAlert = document.getElementById('loginSuccessAlert');
    const countdownElement = document.getElementById('countdown');
    const closeAlertBtn = document.getElementById('closeAlert');
    
    if (!loginSuccessAlert) return;
    
    // Récupérer le rôle depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const role = urlParams.get('role');
    
    let countdown = 3;
    let countdownInterval = null;
    
    // Fonction de redirection
    function redirect() {
        if (role === 'admin') {
            window.location.href = 'admin/dashboard.php';
        } else if (role === 'etudiant') {
            window.location.href = 'etudiant/dashboard.php';
        }
    }
    
    // Démarrer le compte à rebours
    if (countdownElement) {
        countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                redirect();
            }
        }, 1000);
    }
    
    // Permettre de fermer l'alerte et annuler la redirection
    if (closeAlertBtn) {
        closeAlertBtn.addEventListener('click', () => {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            // Nettoyer l'URL
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
    
    // Permettre de cliquer sur l'alerte pour rediriger immédiatement
    loginSuccessAlert.addEventListener('click', (e) => {
        if (e.target !== closeAlertBtn && !closeAlertBtn.contains(e.target)) {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            redirect();
        }
    });
}

// Log de chargement
console.log('Page d\'accueil initialisée avec succès');

