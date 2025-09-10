<?php
/**
 * Redirecionador Principal
 * Sistema de Análise Contratual
 */

// Carregar configurações
require_once __DIR__ . '/config/config.php';

// Obter URI da requisição
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
if (empty($uri)) {
    $uri = '/';
}

// Roteamento simples
switch ($uri) {
    case '/':
        // Landing page
        include __DIR__ . '/landing.php';
        break;
        
    case '/login':
        // Página de login
        include __DIR__ . '/login.php';
        break;
        
    case '/password_recovery':
        // Página de recuperação de senha
        include __DIR__ . '/password_recovery.php';
        break;
        
    case '/home':
        // Dashboard (requer autenticação)
        include __DIR__ . '/home.php';
        break;
        
    case '/contracts':
        // Lista de contratos (requer autenticação)
        include __DIR__ . '/contracts.php';
        break;
        
    case '/my_account':
        // Minha conta (requer autenticação)
        include __DIR__ . '/my_account.php';
        break;
        
    case '/settings':
        // Configurações (requer autenticação)
        include __DIR__ . '/settings.php';
        break;
        
    default:
        // Verificar se é um contrato específico
        if (preg_match('/^\/contract\/(.+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            include __DIR__ . '/contract.php';
            break;
        }
        
        // Verificar se é um relatório de contrato
        if (preg_match('/^\/contract_report\/(.+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            include __DIR__ . '/contract_report.php';
            break;
        }
        
        // 404 - Página não encontrada
        http_response_code(404);
        include __DIR__ . '/partials/404.php';
        break;
}