/**
 * JavaScript para a página de Contratos
 * Sistema ROSS - Analista Jurídico
 * Versão mínima - apenas funcionalidades essenciais
 */

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página de contratos
    if (document.querySelector('.contracts-page')) {
        initContractsPage();
    }
});

/**
 * Inicializar funcionalidades da página de contratos
 */
function initContractsPage() {
    // Apenas funcionalidades essenciais que não interferem com modais
    initFilters();
    initAutoCloseAlerts();
}

/**
 * Inicializar funcionalidades dos filtros
 */
function initFilters() {
    // Auto-submit quando status muda
    const statusFilter = document.querySelector('.contracts-page #statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            autoSubmitFilters();
        });
    }

    // Auto-submit quando datas mudam
    const dateInputs = document.querySelectorAll('.contracts-page input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', () => {
            autoSubmitFilters();
        });
    });

    // Auto-submit com debounce na busca
    const searchInput = document.querySelector('.contracts-page #searchTerm');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                autoSubmitFilters();
            }, 500);
        });
    }
}

/**
 * Auto-submit dos filtros
 */
function autoSubmitFilters() {
    const form = document.querySelector('.contracts-page form[method="GET"]');
    if (form) {
        form.submit();
    }
}

/**
 * Inicializar fechamento automático dos alerts
 */
function initAutoCloseAlerts() {
    // Encontrar todos os alerts do Bootstrap na página
    const alerts = document.querySelectorAll('.contracts-page .alert');
    
    alerts.forEach(alert => {
        // Adicionar botão de fechar se não existir
        if (!alert.querySelector('.btn-close')) {
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'btn-close';
            closeButton.setAttribute('aria-label', 'Close');
            closeButton.setAttribute('data-bs-dismiss', 'alert');
            alert.appendChild(closeButton);
        }
        
        // Fechar automaticamente em 5 segundos
        setTimeout(() => {
            if (alert && alert.parentNode) {
                // Usar Bootstrap para fechar o alert
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
}
