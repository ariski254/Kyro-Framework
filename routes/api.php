<?php

use Core\Router;
use Core\Request;
use Core\Response;

// Sample API Routes
Router::get('/status', function () {
    return [
        'status' => 'success',
        'framework' => 'Kyro',
        'version' => '1.0.0',
        'php_version' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s'),
    ];
});

Router::get('/user/{id}', function ($id) {
    return [
        'user_id' => (int)$id,
        'name' => 'Developer Kyro',
        'email' => 'dev@kyro.local',
        'message' => "Detail user dengan ID: {$id}",
    ];
});
