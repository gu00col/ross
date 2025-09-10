<?php
/**
 * Configurações de Banco de Dados
 * Sistema de Análise Contratual
 */

return [
    // Driver padrão
    'default' => $_ENV['DB_CONNECTION'] ?? 'postgresql',

    // Conexões disponíveis
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
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
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
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],

    // Configurações de migração
    'migrations' => [
        'table' => 'migrations',
        'path' => app_path('Database/Migrations'),
    ],

    // Configurações de seed
    'seeds' => [
        'path' => app_path('Database/Seeds'),
    ],
];
