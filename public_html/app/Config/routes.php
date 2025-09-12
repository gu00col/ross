<?php
/**
 * Configuração de rotas do sistema ROSS
 * Sistema de roteamento amigável
 */

return [
    // Rotas públicas (não requerem autenticação)
    'public' => [
        'login' => 'login.php',
        'cadastro' => 'register.php',
        'recuperar-senha' => 'password_recovery.php',
        'logout' => 'logout.php',
    ],
    
    // Rotas protegidas (requerem autenticação)
    'protected' => [
        'home' => 'home.php',
        'dashboard' => 'dashboard.php',
        'contratos' => 'contracts.php',
        'perfil' => 'profile.php',
        'configuracoes' => 'settings.php',
    ],
    
    // Rotas da API
    'api' => [
        'auth' => 'api/auth/index.php',
        'contracts' => 'api/contracts/index.php',
        'users' => 'api/users/index.php',
    ],
    
    // Redirecionamentos especiais
    'redirects' => [
        '/' => 'login.php', // Página inicial
        '/index' => 'login.php',
        '/inicio' => 'home.php',
        '/painel' => 'dashboard.php',
    ]
];

