/**
 * JavaScript global para Floating Labels
 * Sistema ROSS - Analista Jurídico
 * Para uso em todo o sistema
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Floating Labels
    initializeFloatingLabels();
    
    // Configurar validação em tempo real
    setupRealTimeValidation();
    
    // Configurar efeitos visuais
    setupVisualEffects();
});

/**
 * Inicializa todos os Floating Labels da página
 */
function initializeFloatingLabels() {
    const floatingInputs = document.querySelectorAll('.form-floating .form-control');
    
    floatingInputs.forEach(input => {
        // Verificar se o campo já tem valor (para casos de preenchimento automático)
        if (input.value) {
            input.classList.add('has-value');
        }
        
        // Adicionar evento para detectar mudanças
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    });
}

/**
 * Configura validação em tempo real para Floating Labels
 */
function setupRealTimeValidation() {
    const floatingInputs = document.querySelectorAll('.form-floating .form-control');
    
    floatingInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Remover classes de validação anteriores
            this.classList.remove('is-invalid', 'is-valid');
            
            // Validar campo se tiver valor
            if (this.value) {
                validateField(this);
            }
        });
        
        input.addEventListener('blur', function() {
            // Validar campo ao sair
            if (this.value) {
                validateField(this);
            }
        });
    });
}

/**
 * Configura efeitos visuais para Floating Labels
 */
function setupVisualEffects() {
    const floatingInputs = document.querySelectorAll('.form-floating .form-control');
    
    floatingInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
        });
    });
}

/**
 * Valida um campo individual
 * @param {HTMLElement} field - Campo a ser validado
 */
function validateField(field) {
    const fieldType = field.type;
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Validação básica de campo obrigatório
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo é obrigatório';
    }
    
    // Validações específicas por tipo
    if (value && isValid) {
        switch (fieldType) {
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'E-mail inválido';
                }
                break;
                
            case 'password':
                if (value.length < 6) {
                    isValid = false;
                    errorMessage = 'Senha deve ter pelo menos 6 caracteres';
                }
                break;
                
            case 'tel':
                const phoneRegex = /^[\d\s\(\)\-\+]+$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Telefone inválido';
                }
                break;
                
            case 'url':
                try {
                    new URL(value);
                } catch {
                    isValid = false;
                    errorMessage = 'URL inválida';
                }
                break;
        }
    }
    
    // Aplicar resultado da validação
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        field.setCustomValidity('');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        field.setCustomValidity(errorMessage);
    }
    
    return isValid;
}

/**
 * Valida todo o formulário
 * @param {HTMLElement} form - Formulário a ser validado
 * @returns {boolean} True se válido, false caso contrário
 */
function validateForm(form) {
    const floatingInputs = form.querySelectorAll('.form-floating .form-control');
    let isFormValid = true;
    
    floatingInputs.forEach(input => {
        if (!validateField(input)) {
            isFormValid = false;
        }
    });
    
    // Adicionar classe de validação do Bootstrap
    form.classList.add('was-validated');
    
    return isFormValid;
}

/**
 * Limpa validação de um formulário
 * @param {HTMLElement} form - Formulário a ser limpo
 */
function clearFormValidation(form) {
    const floatingInputs = form.querySelectorAll('.form-floating .form-control');
    
    floatingInputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
        input.setCustomValidity('');
    });
    
    form.classList.remove('was-validated');
}

/**
 * Configura validação para um formulário específico
 * @param {HTMLElement} form - Formulário a ser configurado
 */
function setupFormValidation(form) {
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (validateForm(this)) {
            // Formulário válido - processar
            console.log('Formulário válido - pronto para processar');
            // Aqui você pode adicionar a lógica de processamento
        } else {
            console.log('Formulário inválido');
        }
    });
}

// Exportar funções para uso global
window.FloatingLabels = {
    initialize: initializeFloatingLabels,
    validateField: validateField,
    validateForm: validateForm,
    clearValidation: clearFormValidation,
    setupForm: setupFormValidation
};

