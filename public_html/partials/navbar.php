<?php
/**
 * Navbar - Barra de navegação superior
 * Sistema ROSS - Analista Jurídico
 */

// Verificar se usuário está logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="navbar-content">
        <!-- Botão do menu mobile -->
        <button class="navbar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Título da página -->
        <div class="navbar-title">
            <h1 class="page-title" id="pageTitle">Dashboard</h1>
            <p class="page-subtitle" id="pageSubtitle">Visão geral do sistema</p>
        </div>

        <!-- Ações do usuário -->
        <div class="navbar-actions">
            <!-- Notificações -->
            <div class="notification-dropdown">
                <button class="notification-btn" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge">3</span>
                </button>
                <div class="notification-menu" id="notificationMenu">
                    <div class="notification-header">
                        <h6>Notificações</h6>
                        <button class="mark-all-read">Marcar todas como lidas</button>
                    </div>
                    <div class="notification-list">
                        <div class="notification-item unread">
                            <div class="notification-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Novo contrato adicionado</p>
                                <span class="notification-time">Há 5 minutos</span>
                            </div>
                        </div>
                        <div class="notification-item unread">
                            <div class="notification-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Contrato vencendo em 3 dias</p>
                                <span class="notification-time">Há 1 hora</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Análise concluída</p>
                                <span class="notification-time">Há 2 horas</span>
                            </div>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="/notificacoes" class="view-all">Ver todas as notificações</a>
                    </div>
                </div>
            </div>

            <!-- Menu do usuário -->
            <div class="user-dropdown">
                <button class="user-btn" id="userBtn">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="user-menu" id="userMenu">
                    <div class="user-menu-header">
                        <div class="user-avatar-large">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h6><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></h6>
                            <p><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'usuario@exemplo.com'); ?></p>
                        </div>
                    </div>
                    <div class="user-menu-items">
                        <a href="/perfil" class="user-menu-item">
                            <i class="fas fa-user-circle"></i>
                            <span>Meu Perfil</span>
                        </a>
                        <a href="/configuracoes" class="user-menu-item">
                            <i class="fas fa-cog"></i>
                            <span>Configurações</span>
                        </a>
                        <a href="/ajuda" class="user-menu-item">
                            <i class="fas fa-question-circle"></i>
                            <span>Ajuda</span>
                        </a>
                        <div class="user-menu-divider"></div>
                        <a href="/logout" class="user-menu-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
/* Navbar Styles */
.navbar {
    height: 70px;
    background: white;
    border-bottom: 1px solid #e5e7eb;
    position: fixed;
    top: 0;
    left: 280px;
    right: 0;
    z-index: 999;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.navbar-content {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 2rem;
    gap: 1.5rem;
}

/* Botão do menu mobile */
.navbar-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--ross-blue);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.navbar-toggle:hover {
    background: #f3f4f6;
}

/* Título da página */
.navbar-title {
    flex: 1;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--ross-blue);
    margin: 0;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 0.9rem;
    color: var(--ross-text-muted);
    margin: 0;
    font-weight: 400;
}

/* Ações do usuário */
.navbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Notificações */
.notification-dropdown {
    position: relative;
}

.notification-btn {
    position: relative;
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--ross-text-muted);
    cursor: pointer;
    padding: 0.75rem;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.notification-btn:hover {
    background: #f3f4f6;
    color: var(--ross-blue);
}

.notification-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    line-height: 1;
}

.notification-menu {
    position: absolute;
    top: 100%;
    right: 0;
    width: 350px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.notification-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.notification-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h6 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--ross-blue);
}

.mark-all-read {
    background: none;
    border: none;
    color: var(--ross-light-blue);
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.mark-all-read:hover {
    background: #f3f4f6;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.3s ease;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item.unread {
    background: #fef3c7;
}

.notification-icon {
    width: 40px;
    height: 40px;
    background: var(--ross-light-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.notification-icon i {
    color: var(--ross-beige);
    font-size: 1rem;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-text {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    color: var(--ross-text-dark);
    line-height: 1.4;
}

.notification-time {
    font-size: 0.8rem;
    color: var(--ross-text-muted);
}

.notification-footer {
    padding: 1rem;
    text-align: center;
    border-top: 1px solid #e5e7eb;
}

.view-all {
    color: var(--ross-light-blue);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.view-all:hover {
    text-decoration: underline;
}

/* Menu do usuário */
.user-dropdown {
    position: relative;
}

.user-btn {
    display: flex;
    align-items: center;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 12px;
    transition: background-color 0.3s ease;
    gap: 0.75rem;
}

.user-btn:hover {
    background: #f3f4f6;
}

.user-avatar {
    width: 36px;
    height: 36px;
    background: var(--ross-light-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-avatar i {
    color: var(--ross-beige);
    font-size: 1rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ross-blue);
}

.user-info i {
    font-size: 0.8rem;
    color: var(--ross-text-muted);
}

.user-menu {
    position: absolute;
    top: 100%;
    right: 0;
    width: 280px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.user-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.user-menu-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar-large {
    width: 50px;
    height: 50px;
    background: var(--ross-light-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-avatar-large i {
    color: var(--ross-beige);
    font-size: 1.2rem;
}

.user-details h6 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--ross-blue);
}

.user-details p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--ross-text-muted);
}

.user-menu-items {
    padding: 0.5rem 0;
}

.user-menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: var(--ross-text-dark);
    text-decoration: none;
    transition: background-color 0.3s ease;
    gap: 0.75rem;
}

.user-menu-item:hover {
    background: #f9fafb;
    color: var(--ross-text-dark);
}

.user-menu-item i {
    width: 20px;
    text-align: center;
    color: var(--ross-text-muted);
}

.user-menu-item.logout {
    color: #ef4444;
}

.user-menu-item.logout:hover {
    background: #fef2f2;
    color: #dc2626;
}

.user-menu-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 0.5rem 0;
}

/* Responsividade */
@media (max-width: 768px) {
    .navbar {
        left: 0;
    }
    
    .navbar-toggle {
        display: block;
    }
    
    .navbar-title {
        display: none;
    }
    
    .notification-menu,
    .user-menu {
        width: 300px;
    }
}

/* Variáveis CSS */
:root {
    --ross-blue: #0D2149;
    --ross-light-blue: #1a3a7a;
    --ross-beige: #F9EBE0;
    --ross-dark-beige: #f2e0c7;
    --ross-text-dark: #1e293b;
    --ross-text-muted: #64748b;
}
</style>

<!-- JavaScript removido para evitar interferência com modais -->
