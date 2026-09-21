<?php

use Core\Router;
use App\Controllers\HomeController;

Router::get('/', [HomeController::class, 'index'])->name('home');

Router::get('/hello', function () {
    return "Hello from Kyro Framework! 🚀";
});

Router::get('/hello/{name}', function ($name) {
    return "Halo, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "! Selamat datang di Kyro Framework.";
});