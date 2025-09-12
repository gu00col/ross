<?php
/**
 * Arquivo Principal de Configuração
 * Sistema de Análise Contratual
 */

// Carregar variáveis de ambiente
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Definir constantes da aplicação
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistema de Análise Contratual');
define('APP_VERSION', $_ENV['APP_VERSION'] ?? '1.0.0');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8080');
define('APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Definir timezone
date_default_timezone_set(APP_TIMEZONE);

// Definir constantes de diretórios
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Definir constantes de banco de dados
define('DB_CONNECTION', $_ENV['DB_CONNECTION'] ?? 'postgresql');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'pgverctor');
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');
define('DB_DATABASE', $_ENV['DB_DATABASE'] ?? 'ross');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'postgres');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'postgres123');

// Definir constantes do Redis
define('REDIS_HOST', $_ENV['REDIS_HOST'] ?? 'redis');
define('REDIS_PORT', $_ENV['REDIS_PORT'] ?? '6379');
define('REDIS_PASSWORD', $_ENV['REDIS_PASSWORD'] ?? null);
define('REDIS_DATABASE', $_ENV['REDIS_DATABASE'] ?? 0);

// Definir constantes do N8N
define('N8N_URL', $_ENV['N8N_URL'] ?? 'http://localhost:5678');
define('N8N_WEBHOOK_ID', $_ENV['N8N_WEBHOOK_ID'] ?? '96a31298-7d8d-4006-b434-40917d08a9b0');
define('N8N_API_KEY', $_ENV['N8N_API_KEY'] ?? '');

// Definir constantes do Google Drive
define('GOOGLE_DRIVE_CLIENT_ID', $_ENV['GOOGLE_DRIVE_CLIENT_ID'] ?? '');
define('GOOGLE_DRIVE_CLIENT_SECRET', $_ENV['GOOGLE_DRIVE_CLIENT_SECRET'] ?? '');
define('GOOGLE_DRIVE_FOLDER_ID', $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '1HhcJIkTP47ACAiQo1MPwOUJ3aTD7Syjr');

// Definir constantes do Google Gemini
define('GOOGLE_GEMINI_API_KEY', $_ENV['GOOGLE_GEMINI_API_KEY'] ?? '');

// Configurações de erro
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 em produção com HTTPS

// Função para carregar configurações
function config($key = null, $default = null) {
    static $config = null;
    
    if ($config === null) {
        $config = require CONFIG_PATH . '/app.php';
    }
    
    if ($key === null) {
        return $config;
    }
    
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }
    
    return $value;
}

// Função para obter caminho da aplicação
function app_path($path = '') {
    return APP_PATH . ($path ? '/' . ltrim($path, '/') : '');
}

// Função para obter caminho de storage
function storage_path($path = '') {
    return STORAGE_PATH . ($path ? '/' . ltrim($path, '/') : '');
}

// Função para obter caminho de configuração
function config_path($path = '') {
    return CONFIG_PATH . ($path ? '/' . ltrim($path, '/') : '');
}

// Função para obter caminho público
function public_path($path = '') {
    return PUBLIC_PATH . ($path ? '/' . ltrim($path, '/') : '');
}

// Função para obter URL da aplicação
function app_url($path = '') {
    return APP_URL . ($path ? '/' . ltrim($path, '/') : '');
}

// Função para obter URL de assets
function asset_url($path = '') {
    return app_url('assets/' . ltrim($path, '/'));
}

// Função para obter URL de uploads
function upload_url($path = '') {
    return app_url('uploads/' . ltrim($path, '/'));
}

// Função para obter URL de storage
function storage_url($path = '') {
    return app_url('storage/' . ltrim($path, '/'));
}
