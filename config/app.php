<?php

use Core\Env;

return [
    'name' => Env::get('APP_NAME', 'Kyro'),
    'env' => Env::get('APP_ENV', 'local'),
    'debug' => Env::get('APP_DEBUG', true),
    'url' => Env::get('APP_URL', 'http://localhost:8000'),
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'key' => Env::get('APP_KEY', 'kyro-app-secret-key-32chars-long-demo'),
];