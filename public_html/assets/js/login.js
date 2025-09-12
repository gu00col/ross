/**
 * JavaScript para a página de login
 * Sistema ROSS - Analista Jurídico
 * Usando Bootstrap 5 como base
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos do formulário
    const form = document.querySelector('.needs-validation');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitButton = document.querySelector('.btn-primary');

    // Validação em tempo real
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);

    // Validação do formulário usando Bootstrap
    form.addEventListener('submit', function(event) {
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        
        if (isEmailValid && isPasswordValid) {
            // Formulário válido - permitir envio
            console.log('Formulário válido - enviando para servidor');
            showLoadingState();
            // Não usar preventDefault() - deixar o formulário ser enviado
        } else {
            // Formulário inválido - prevenir envio
            event.preventDefault();
            console.log('Formulário inválido - envio cancelado');
        }
        
        // Adicionar classe de validação do Bootstrap
        form.classList.add('was-validated');
    });

    /**
     * Valida o campo de e-mail
     * @returns {boolean} True se válido, false caso contrário
     */
    function validateEmail() {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email === '') {
            emailInput.setCustomValidity('E-mail é obrigatório');
            return false;
        } else if (!emailRegex.test(email)) {
            emailInput.setCustomValidity('E-mail inválido');
            return false;
        } else {
            emailInput.setCustomValidity('');
            return true;
        }
    }

    /**
     * Valida o campo de senha
     * @returns {boolean} True se válido, false caso contrário
     */
    function validatePassword() {
        const password = passwordInput.value;
        
        if (password === '') {
            passwordInput.setCustomValidity('Senha é obrigatória');
            return false;
        } else if (password.length < 6) {
            passwordInput.setCustomValidity('Senha deve ter pelo menos 6 caracteres');
            return false;
        } else {
            passwordInput.setCustomValidity('');
            return true;
        }
    }

    /**
     * Exibe estado de carregamento
     */
    function showLoadingState() {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Entrando...';
        submitButton.classList.add('loading');
    }

    /**
     * Remove estado de carregamento
     */
    function hideLoadingState() {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Entrar';
        submitButton.classList.remove('loading');
    }

    // Efeitos visuais nos campos Floating Labels
    const floatingInputs = document.querySelectorAll('.form-floating .form-control');
    floatingInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
        });
    });

    // Validação em tempo real para melhor UX com Floating Labels
    floatingInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
            }
            if (this.classList.contains('is-valid')) {
                this.classList.remove('is-valid');
            }
        });
    });
});

// Adicionar estilos CSS para estados customizados com Floating Labels
const style = document.createElement('style');
style.textContent = `
    .btn-primary.loading {
        opacity: 0.8;
        cursor: not-allowed;
    }
    
    .form-floating.focused .form-control {
        border-color: var(--ross-blue);
        box-shadow: 0 0 0 3px rgba(13, 33, 73, 0.1);
    }
    
    .form-floating.focused label {
        color: var(--ross-blue);
    }
    
    .form-floating > .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-floating > .form-control.is-invalid ~ label {
        color: #dc3545;
    }
    
    .form-floating > .form-control.is-valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 1.06 1.06L6.73 5.3l.94.94L4.3 9.73z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-floating > .form-control.is-valid ~ label {
        color: #198754;
    }
`;
document.head.appendChild(style);