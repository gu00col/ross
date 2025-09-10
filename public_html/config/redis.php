<?php
/**
 * Configurações do Redis
 * Sistema de Análise Contratual
 */

return [
    // Conexão padrão
    'default' => $_ENV['REDIS_CONNECTION'] ?? 'cache',

    // Conexões disponíveis
    'connections' => [
        'cache' => [
            'host' => $_ENV['REDIS_HOST'] ?? 'redis',
            'port' => $_ENV['REDIS_PORT'] ?? '6379',
            'password' => $_ENV['REDIS_PASSWORD'] ?? null,
            'database' => $_ENV['REDIS_DATABASE'] ?? 0,
            'timeout' => 5,
            'read_timeout' => 5,
            'persistent' => false,
        ],
        'session' => [
            'host' => $_ENV['REDIS_HOST'] ?? 'redis',
            'port' => $_ENV['REDIS_PORT'] ?? '6379',
            'password' => $_ENV['REDIS_PASSWORD'] ?? null,
            'database' => $_ENV['REDIS_DATABASE'] ?? 1,
            'timeout' => 5,
            'read_timeout' => 5,
            'persistent' => false,
        ],
        'queue' => [
            'host' => $_ENV['REDIS_HOST'] ?? 'redis',
            'port' => $_ENV['REDIS_PORT'] ?? '6379',
            'password' => $_ENV['REDIS_PASSWORD'] ?? null,
            'database' => $_ENV['REDIS_DATABASE'] ?? 2,
            'timeout' => 5,
            'read_timeout' => 5,
            'persistent' => false,
        ],
    ],

    // Configurações de cluster (para produção)
    'clusters' => [
        'default' => [
            [
                'host' => $_ENV['REDIS_CLUSTER_HOST_1'] ?? 'redis-1',
                'port' => $_ENV['REDIS_CLUSTER_PORT_1'] ?? '6379',
                'password' => $_ENV['REDIS_CLUSTER_PASSWORD_1'] ?? null,
            ],
            [
                'host' => $_ENV['REDIS_CLUSTER_HOST_2'] ?? 'redis-2',
                'port' => $_ENV['REDIS_CLUSTER_PORT_2'] ?? '6379',
                'password' => $_ENV['REDIS_CLUSTER_PASSWORD_2'] ?? null,
            ],
        ],
    ],

    // Configurações de cache
    'cache' => [
        'prefix' => $_ENV['REDIS_PREFIX'] ?? 'contract_analysis:',
        'ttl' => $_ENV['REDIS_TTL'] ?? 3600, // 1 hora
    ],

    // Configurações de sessão
    'session' => [
        'prefix' => 'contract_analysis_session:',
        'ttl' => $_ENV['SESSION_LIFETIME'] ?? 7200, // 2 horas
    ],
];
