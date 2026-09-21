<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use Core\Router;
use Core\Response;

class HomeController extends Controller {
    public function index(): Response {
        $dbStatus = false;
        $dbError = null;

        try {
            Database::connect();
            $dbStatus = true;
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        $routesCount = count(Router::getRoutes());

        return $this->view('home', [
            'title' => 'Kyro Framework',
            'version' => 'v1.0.0',
            'php_version' => PHP_VERSION,
            'app_env' => config('app.env', 'local'),
            'app_debug' => config('app.debug', true),
            'db_status' => $dbStatus,
            'db_error' => $dbError,
            'routes_count' => $routesCount,
            'timezone' => config('app.timezone', 'UTC'),
        ]);
    }
}