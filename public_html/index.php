<?php
/**
 * Sistema ROSS - Analista Jurídico
 * Ponto de entrada principal da aplicação
 */

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carregar autoloader do Composer
require_once __DIR__ . '/vendor/autoload.php';

// Carregar configurações da aplicação
$app = new \App\Config\App();

// Executar aplicação
$app->run();