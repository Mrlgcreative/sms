/**
 * Script JavaScript pour la page de paiement ultra-moderne
 * Système de Gestion Scolaire - Module Comptable
 */

// Configuration globale
const PaymentConfig = {
    animation: {
        duration: 300,
        easing: 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'
    },
    validation: {
        debounceDelay: 500
    },
    currency: {
        symbol: 'FCFA',
        locale: 'fr-FR'
    }
};

// Classe principale pour la gestion des paiements
class ModernPaymentManager {
    constructor() {
        this.form = null;
        this.formElements = {};
        this.validationRules = {};
        this.init();
    }

    init() {
        this.loadDOM();
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupValidation();
        this.formatAmounts();
        this.initializeSelect2();
    }

    loadDOM() {
        this.form = document.querySelector('form');
        this.formElements = {
            eleve: document.querySelector('select[name="eleve_id"]'),
            frais: document.querySelector('select[name="frais_id"]'),
            montant: document.querySelector('input[name="montant"]'),
            datePaiement: document.querySelector('input[name="date_paiement"]'),
            methode: document.querySelector('select[name="methode_paiement"]'),
            reference: document.querySelector('input[name="reference"]'),
            observation: document.querySelector('textarea[name="observation"]')
        };
    }

    setupEventListeners() {
        // Événements pour les champs de formulaire
        Object.keys(this.formElements).forEach(key => {
            const element = this.formElements[key];
            if (element) {
                element.addEventListener('focus', this.handleFieldFocus.bind(this));
                element.addEventListener('blur', this.handleFieldBlur.bind(this));
                element.addEventListener('input', this.handleFieldInput.bind(this));
            }
        });

        // Événement pour la soumission du formulaire
        if (this.form) {
            this.form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }

        // Événements pour l'amélioration UX
        this.setupHoverEffects();
        this.setupKeyboardNavigation();
    }

    handleFieldFocus(event) {
        const field = event.target;
        const container = field.closest('.form-group, .form-group-modern');
        
        if (container) {
            container.classList.add('focused');
            this.animateFieldFocus(field);
        }
    }

    handleFieldBlur(event) {
        const field = event.target;
        const container = field.closest('.form-group, .form-group-modern');
        
        if (container) {
            container.classList.remove('focused');
            this.validateField(field);
        }
    }

    handleFieldInput(event) {
        const field = event.target;
        
        // Validation en temps réel avec debounce
        clearTimeout(this.validationTimeout);
        this.validationTimeout = setTimeout(() => {
            this.validateField(field);
        }, PaymentConfig.validation.debounceDelay);

        // Formatage automatique pour les montants
        if (field.name === 'montant') {
            this.formatMoneyInput(field);
        }

        // Mise à jour du résumé en temps réel
        this.updatePaymentSummary();
    }

    handleFormSubmit(event) {
        event.preventDefault();
        
        if (this.validateForm()) {
            this.showLoadingState();
            this.submitForm();
        } else {
            this.showValidationErrors();
        }
    }

    animateFieldFocus(field) {
        // Animation subtile lors du focus
        field.style.transform = 'translateY(-2px)';
        field.style.transition = `all ${PaymentConfig.animation.duration}ms ${PaymentConfig.animation.easing}`;
        
        setTimeout(() => {
            field.style.transform = '';
        }, PaymentConfig.animation.duration);
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        switch (field.name) {
            case 'eleve_id':
                isValid = value !== '';
                message = 'Veuillez sélectionner un élève';
                break;
            
            case 'frais_id':
                isValid = value !== '';
                message = 'Veuillez sélectionner un type de frais';
                break;
            
            case 'montant':
                const amount = parseFloat(value.replace(/[^\d.]/g, ''));
                isValid = !isNaN(amount) && amount > 0;
                message = 'Veuillez saisir un montant valide';
                break;
            
            case 'date_paiement':
                isValid = value !== '' && new Date(value) <= new Date();
                message = 'Veuillez saisir une date valide';
                break;
            
            case 'methode_paiement':
                isValid = value !== '';
                message = 'Veuillez sélectionner une méthode de paiement';
                break;
            
            case 'reference':
                if (field.required) {
                    isValid = value !== '';
                    message = 'La référence est requise pour cette méthode';
                }
                break;
        }

        this.updateFieldValidation(field, isValid, message);
        return isValid;
    }

    updateFieldValidation(field, isValid, message) {
        const container = field.closest('.form-group, .form-group-modern');
        
        // Supprimer les classes existantes
        field.classList.remove('field-error', 'field-success', 'validation-error', 'validation-success');
        
        // Supprimer les messages existants
        const existingMessage = container.querySelector('.error-message-modern, .success-message-modern');
        if (existingMessage) {
            existingMessage.remove();
        }

        if (isValid) {
            field.classList.add('field-success', 'validation-success');
        } else {
            field.classList.add('field-error', 'validation-error');
            
            // Ajouter le message d'erreur
            const messageElement = document.createElement('div');
            messageElement.className = 'error-message-modern';
            messageElement.textContent = message;
            container.appendChild(messageElement);
        }
    }

    validateForm() {
        let isValid = true;
        
        Object.keys(this.formElements).forEach(key => {
            const element = this.formElements[key];
            if (element && element.required) {
                if (!this.validateField(element)) {
                    isValid = false;
                }
            }
        });

        return isValid;
    }

    formatMoneyInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        
        if (value) {
            // Formatage avec espaces comme séparateurs de milliers
            value = parseInt(value).toLocaleString('fr-FR');
            input.value = value;
        }
    }

    formatAmounts() {
        // Formater tous les affichages de montants
        const amountElements = document.querySelectorAll('.amount-display, .amount-display-modern');
        
        amountElements.forEach(element => {
            const amount = parseFloat(element.textContent.replace(/[^\d.]/g, ''));
            if (!isNaN(amount)) {
                element.textContent = this.formatCurrency(amount);
            }
        });
    }

    formatCurrency(amount) {
        return amount.toLocaleString(PaymentConfig.currency.locale) + ' ' + PaymentConfig.currency.symbol;
    }

    updatePaymentSummary() {
        const montantField = this.formElements.montant;
        const summaryElement = document.querySelector('.payment-summary-modern, .payment-summary');
        
        if (montantField && summaryElement) {
            const amount = parseFloat(montantField.value.replace(/[^\d.]/g, ''));
            
            if (!isNaN(amount) && amount > 0) {
                // Mettre à jour le résumé
                this.updateSummaryDisplay(summaryElement, amount);
            }
        }
    }

    updateSummaryDisplay(container, amount) {
        const totalElement = container.querySelector('.summary-total, .amount-display');
        
        if (totalElement) {
            totalElement.textContent = this.formatCurrency(amount);
            
            // Animation subtile
            totalElement.style.transform = 'scale(1.05)';
            setTimeout(() => {
                totalElement.style.transform = 'scale(1)';
            }, 200);
        }
    }

    initializeSelect2() {
        // Configuration Select2 pour une expérience moderne
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('select').not('.no-select2').select2({
                theme: 'default',
                width: '100%',
                language: 'fr',
                placeholder: 'Sélectionnez une option...',
                allowClear: true,
                escapeMarkup: function(markup) {
                    return markup;
                }
            }).addClass('modern');

            // Événements personnalisés pour Select2
            $('select').on('select2:open', function() {
                const $dropdown = $('.select2-dropdown');
                $dropdown.addClass('select2-dropdown-modern');
            });
        }
    }

    showLoadingState() {
        const submitButton = document.querySelector('.btn-submit, .btn-payment-primary');
        const loadingSpinner = document.querySelector('.loading-spinner, .loading-spinner-modern');
        
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Traitement...';
        }
        
        if (loadingSpinner) {
            loadingSpinner.classList.add('show');
        }
    }

    hideLoadingState() {
        const submitButton = document.querySelector('.btn-submit, .btn-payment-primary');
        const loadingSpinner = document.querySelector('.loading-spinner, .loading-spinner-modern');
        
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Enregistrer le paiement';
        }
        
        if (loadingSpinner) {
            loadingSpinner.classList.remove('show');
        }
    }

    submitForm() {
        const formData = new FormData(this.form);
        
        fetch(this.form.action || window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoadingState();
            
            if (data.success) {
                this.showSuccessMessage(data.message || 'Paiement enregistré avec succès');
                this.resetForm();
            } else {
                this.showErrorMessage(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            this.hideLoadingState();
            this.showErrorMessage('Erreur de connexion. Veuillez réessayer.');
            console.error('Erreur:', error);
        });
    }

    showSuccessMessage(message) {
        this.showAlert(message, 'success');
    }

    showErrorMessage(message) {
        this.showAlert(message, 'danger');
    }

    showAlert(message, type) {
        const alertHTML = `
            <div class="alert alert-${type}-modern alert-modern" style="animation: slideInDown 0.3s ease-out;">
                <div class="alert-icon">
                    ${type === 'success' ? '✅' : '⚠️'}
                </div>
                <div class="alert-content">
                    ${message}
                </div>
            </div>
        `;
        
        const container = document.querySelector('.content-wrapper, .payment-form-container');
        if (container) {
            container.insertAdjacentHTML('afterbegin', alertHTML);
            
            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                const alert = container.querySelector('.alert-modern');
                if (alert) {
                    alert.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        }
    }

    resetForm() {
        if (this.form) {
            this.form.reset();
            
            // Réinitialiser Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('select').val(null).trigger('change');
            }
            
            // Supprimer toutes les validations
            this.form.querySelectorAll('.field-error, .field-success').forEach(field => {
                field.classList.remove('field-error', 'field-success', 'validation-error', 'validation-success');
            });
            
            this.form.querySelectorAll('.error-message-modern, .success-message-modern').forEach(message => {
                message.remove();
            });
        }
    }

    setupHoverEffects() {
        // Effets de survol pour les cartes
        const cards = document.querySelectorAll('.form-section, .form-section-modern, .payment-summary, .payment-summary-modern');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-2px)';
                card.style.transition = `all ${PaymentConfig.animation.duration}ms ease`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });
    }

    setupKeyboardNavigation() {
        // Navigation au clavier améliorée
        document.addEventListener('keydown', (event) => {
            // Échapper pour annuler les actions
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal, .overlay');
                modals.forEach(modal => modal.style.display = 'none');
            }
            
            // Ctrl+S pour sauvegarder
            if (event.ctrlKey && event.key === 's') {
                event.preventDefault();
                const submitButton = document.querySelector('.btn-submit, .btn-payment-primary');
                if (submitButton) {
                    submitButton.click();
                }
            }
        });
    }

    initializeAnimations() {
        // Animation d'entrée séquentielle
        const animatedElements = document.querySelectorAll('.form-section, .form-section-modern');
        
        animatedElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = `all ${PaymentConfig.animation.duration * 2}ms ${PaymentConfig.animation.easing}`;
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    showValidationErrors() {
        // Faire défiler vers la première erreur
        const firstError = document.querySelector('.field-error, .validation-error');
        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            
            // Animation d'attention
            firstError.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                firstError.style.animation = '';
            }, 500);
        }
    }
}

// Utilitaires supplémentaires
class PaymentUtils {
    static formatPhone(input) {
        let value = input.value.replace(/\D/g, '');
        
        if (value.length >= 8) {
            value = value.replace(/(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4');
        }
        
        input.value = value;
    }

    static validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    static generateReference() {
        const timestamp = Date.now().toString();
        const random = Math.random().toString(36).substr(2, 5).toUpperCase();
        return `PAY${timestamp.slice(-6)}${random}`;
    }

    static copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            console.log('Copié dans le presse-papiers');
        });
    }
}

// Initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    new ModernPaymentManager();
    
    // Ajouter des événements globaux
    document.addEventListener('click', (event) => {
        // Fermer les dropdowns quand on clique ailleurs
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-open').forEach(dropdown => {
                dropdown.classList.remove('dropdown-open');
            });
        }
    });
});

// Export pour utilisation externe
window.PaymentManager = ModernPaymentManager;
window.PaymentUtils = PaymentUtils;
