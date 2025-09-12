/**
 * JavaScript do Layout Principal
 * Sistema ROSS - Analista Jurídico
 * Versão mínima - apenas funcionalidades essenciais
 */

document.addEventListener('DOMContentLoaded', function() {
    // Apenas funcionalidades essenciais que não interferem com modais
    initSidebar();
    initMobileMenu();
});

/**
 * Inicializar funcionalidades da sidebar
 */
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('show');
            }
        });
    }
    
    // Fechar sidebar ao clicar no overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            mobileOverlay.classList.remove('show');
        });
    }
    
    // Fechar sidebar ao clicar em um link (mobile)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('show');
                }
            }
        });
    });
}

/**
 * Inicializar menu mobile
 */
function initMobileMenu() {
    // Adicionar botão de menu mobile se não existir
    if (!document.getElementById('sidebarToggle')) {
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'sidebarToggle';
            toggleBtn.className = 'btn btn-primary d-md-none position-fixed';
            toggleBtn.style.cssText = 'top: 1rem; left: 1rem; z-index: 1001;';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.appendChild(toggleBtn);
        }
    }
}
