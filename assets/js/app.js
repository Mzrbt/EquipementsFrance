/**
 * Application JavaScript principale
 */

// =====================================================
// INITIALISATION
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initModals();
    initFlashMessages();
});

// =====================================================
// MENU MOBILE
// =====================================================
function initMobileMenu() {
    // Sera implémenté plus tard si besoin
}

function toggleMobileMenu() {
    const nav = document.querySelector('.nav');
    const actions = document.querySelector('.header-actions');
    
    nav.classList.toggle('mobile-active');
    actions.classList.toggle('mobile-active');
}

// =====================================================
// MODALS
// =====================================================
function initModals() {
    // Fermer les modals en cliquant sur l'overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// =====================================================
// MESSAGES FLASH
// =====================================================
function initFlashMessages() {
    // Auto-hide après 5 secondes
    document.querySelectorAll('.flash-message').forEach(flash => {
        setTimeout(() => {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    });
}

// =====================================================
// UTILITAIRES
// =====================================================

/**
 * Fonction pour effectuer des requêtes API
 */
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    const response = await fetch(url, { ...defaultOptions, ...options });
    
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return response.json();
}

/**
 * Fonction pour formater les nombres
 */
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

/**
 * Fonction pour debounce (limiter les appels)
 */
function debounce(func, wait) {
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
 * Fonction pour confirmer une action
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
