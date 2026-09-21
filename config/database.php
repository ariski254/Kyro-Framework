<?php

use Core\Env;

return [
    'default' => Env::get('DB_CONNECTION', 'mysql'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => Env::get('DB_DATABASE', dirname(__DIR__) . '/database/database.sqlite'),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => Env::get('DB_HOST', '127.0.0.1'),
            'port' => Env::get('DB_PORT', '3306'),
            'database' => Env::get('DB_NAME', 'kyro'),
            'username' => Env::get('DB_USER', 'root'),
            'password' => Env::get('DB_PASS', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => Env::get('DB_HOST', '127.0.0.1'),
            'port' => Env::get('DB_PORT', '5432'),
            'database' => Env::get('DB_NAME', 'kyro'),
            'username' => Env::get('DB_USER', 'postgres'),
            'password' => Env::get('DB_PASS', ''),
            'charset' => 'utf8',
        ],
    ],
];
