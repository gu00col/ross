<?php
/**
 * Sidebar - Menu lateral
 * Sistema ROSS - Analista Jurídico
 */

// Verificar se usuário está logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="sidebar">
    <!-- Logo e Branding -->
    <div class="sidebar-header">
        <div class="logo-container">
          <img src="assets/images/logo-ross.png" alt="ROSS Logo" class="" width="80px">
        </div>
        <div class="brand-info">
            <h1 class="brand-name">ROSS</h1>
            <p class="brand-subtitle">Analista Jurídico</p>
        </div>
    </div>

    <!-- Menu de Navegação -->
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/home" class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>">
                    <i class="fas fa-dashboard nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="/contracts" class="nav-link <?php echo $current_page === 'contracts' ? 'active' : ''; ?>">
                    <i class="fas fa-file-contract nav-icon"></i>
                    <span class="nav-text">Contratos</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/settings" class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Configurações</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/my_account" class="nav-link <?php echo $current_page === 'my_account' ? 'active' : ''; ?>">
                    <i class="fas fa-user-circle nav-icon"></i>
                    <span class="nav-text">Minha Conta</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Informações do Usuário -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></span>
                <span class="user-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'usuario@exemplo.com'); ?></span>
            </div>
        </div>
        
        <div class="logout-section">
            <a href="/logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>
</aside>

<style>
/* Sidebar Styles */
.sidebar {
    width: 100%;
    height: 100%;
    background: var(--ross-blue);
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

/* Header do Sidebar */
.sidebar-header {
    padding: 2rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(249, 235, 224, 0.1);
}

.logo-container {
    margin-bottom: 1rem;
}

.logo-image {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 50%;
    background: rgba(249, 235, 224, 0.05);
    padding: 8px;
}

.logo-circle {
    width: 60px;
    height: 60px;
    border: 2px solid var(--ross-beige);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    background: rgba(249, 235, 224, 0.05);
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--ross-beige);
    letter-spacing: 1px;
}

.brand-info {
    text-align: center;
}

.brand-name {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--ross-beige);
    margin: 0 0 0.25rem 0;
    letter-spacing: 2px;
}

.brand-subtitle {
    font-size: 0.9rem;
    color: var(--ross-dark-beige);
    margin: 0;
    font-weight: 400;
}

/* Navegação */
.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
}

.nav-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-item {
    margin: 0.25rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    color: var(--ross-beige);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link:hover {
    background: rgba(249, 235, 224, 0.1);
    color: var(--ross-beige);
}

.nav-link.active {
    background: rgba(249, 235, 224, 0.15);
    color: var(--ross-beige);
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--ross-beige);
}

.nav-icon {
    font-size: 1.2rem;
    width: 24px;
    margin-right: 1rem;
    text-align: center;
}

.nav-text {
    font-size: 1rem;
    font-weight: 500;
}

/* Footer do Sidebar */
.sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid rgba(249, 235, 224, 0.1);
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: rgba(249, 235, 224, 0.05);
    border-radius: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: var(--ross-light-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

.user-avatar i {
    color: var(--ross-beige);
    font-size: 1.1rem;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ross-beige);
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    display: block;
    font-size: 0.8rem;
    color: var(--ross-dark-beige);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.logout-section {
    text-align: center;
}

.logout-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: rgba(249, 235, 224, 0.1);
    color: var(--ross-beige);
    text-decoration: none;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid rgba(249, 235, 224, 0.2);
}

.logout-btn:hover {
    background: rgba(249, 235, 224, 0.2);
    color: var(--ross-beige);
    transform: translateY(-1px);
}

.logout-btn i {
    margin-right: 0.5rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .sidebar {
        display: none;
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
