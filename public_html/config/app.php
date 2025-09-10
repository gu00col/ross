<?php
/**
 * Configurações da Aplicação
 * Sistema de Análise Contratual
 */

return [
    // Configurações Gerais
    'app' => [
        'name' => 'Sistema de Análise Contratual',
        'version' => '1.0.0',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
        'timezone' => 'America/Sao_Paulo',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
        'key' => $_ENV['APP_KEY'] ?? 'base64:your-app-key-here',
    ],

    // Configurações de Banco de Dados
    'database' => [
        'default' => 'postgresql',
        'connections' => [
            'postgresql' => [
                'driver' => 'pgsql',
                'host' => $_ENV['DB_HOST'] ?? 'pgverctor',
                'port' => $_ENV['DB_PORT'] ?? '5432',
                'database' => $_ENV['DB_DATABASE'] ?? 'ross',
                'username' => $_ENV['DB_USERNAME'] ?? 'postgres',
                'password' => $_ENV['DB_PASSWORD'] ?? 'postgres123',
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
                'sslmode' => 'prefer',
            ],
            'mysql' => [
                'driver' => 'mysql',
                'host' => $_ENV['MYSQL_HOST'] ?? 'localhost',
                'port' => $_ENV['MYSQL_PORT'] ?? '3306',
                'database' => $_ENV['MYSQL_DATABASE'] ?? 'contract_analysis',
                'username' => $_ENV['MYSQL_USERNAME'] ?? 'root',
                'password' => $_ENV['MYSQL_PASSWORD'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ],
        ],
    ],

    // Configurações do Redis
    'redis' => [
        'default' => 'cache',
        'connections' => [
            'cache' => [
                'host' => $_ENV['REDIS_HOST'] ?? 'redis',
                'port' => $_ENV['REDIS_PORT'] ?? '6379',
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                'database' => $_ENV['REDIS_DATABASE'] ?? 0,
                'timeout' => 5,
            ],
            'session' => [
                'host' => $_ENV['REDIS_HOST'] ?? 'redis',
                'port' => $_ENV['REDIS_PORT'] ?? '6379',
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                'database' => $_ENV['REDIS_DATABASE'] ?? 1,
                'timeout' => 5,
            ],
        ],
    ],

    // Configurações de Sessão
    'session' => [
        'driver' => $_ENV['SESSION_DRIVER'] ?? 'redis',
        'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 120, // minutos
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => storage_path('sessions'),
        'connection' => 'session',
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'contract_analysis_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
    ],

    // Configurações de Cache
    'cache' => [
        'default' => $_ENV['CACHE_DRIVER'] ?? 'redis',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => storage_path('cache'),
            ],
            'redis' => [
                'driver' => 'redis',
                'connection' => 'cache',
            ],
        ],
    ],

    // Configurações de E-mail
    'mail' => [
        'default' => $_ENV['MAIL_MAILER'] ?? 'smtp',
        'mailers' => [
            'smtp' => [
                'transport' => 'smtp',
                'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
                'port' => $_ENV['MAIL_PORT'] ?? 587,
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
                'username' => $_ENV['MAIL_USERNAME'] ?? '',
                'password' => $_ENV['MAIL_PASSWORD'] ?? '',
                'timeout' => null,
                'auth_mode' => null,
            ],
        ],
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@contractanalysis.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'Sistema de Análise Contratual',
        ],
    ],

    // Configurações do N8N
    'n8n' => [
        'url' => $_ENV['N8N_URL'] ?? 'http://localhost:5678',
        'webhook_id' => $_ENV['N8N_WEBHOOK_ID'] ?? '96a31298-7d8d-4006-b434-40917d08a9b0',
        'api_key' => $_ENV['N8N_API_KEY'] ?? '',
        'timeout' => 30,
    ],

    // Configurações do Google Drive
    'google_drive' => [
        'client_id' => $_ENV['GOOGLE_DRIVE_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['GOOGLE_DRIVE_CLIENT_SECRET'] ?? '',
        'redirect_uri' => $_ENV['GOOGLE_DRIVE_REDIRECT_URI'] ?? '',
        'scopes' => ['https://www.googleapis.com/auth/drive'],
        'folder_id' => $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '1HhcJIkTP47ACAiQo1MPwOUJ3aTD7Syjr',
    ],

    // Configurações do Google Gemini
    'google_gemini' => [
        'api_key' => $_ENV['GOOGLE_GEMINI_API_KEY'] ?? '',
        'model' => 'gemini-pro',
        'max_tokens' => 64000,
        'temperature' => 0.2,
    ],

    // Configurações de Upload
    'upload' => [
        'max_file_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? 10485760, // 10MB
        'allowed_types' => ['pdf'],
        'path' => storage_path('uploads'),
        'temp_path' => storage_path('temp'),
    ],

    // Configurações de Log
    'log' => [
        'default' => $_ENV['LOG_CHANNEL'] ?? 'file',
        'channels' => [
            'file' => [
                'driver' => 'file',
                'path' => storage_path('logs/app.log'),
                'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            ],
            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/app.log'),
                'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
                'days' => 14,
            ],
        ],
    ],

    // Configurações de Segurança
    'security' => [
        'password_min_length' => 8,
        'password_require_uppercase' => true,
        'password_require_lowercase' => true,
        'password_require_numbers' => true,
        'password_require_symbols' => false,
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // minutos
        'session_timeout' => 120, // minutos
    ],

    // Configurações de Paginação
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
        'page_param' => 'page',
    ],

    // Configurações de API
    'api' => [
        'rate_limit' => 60, // requests per minute
        'timeout' => 30,
        'version' => 'v1',
    ],
];
