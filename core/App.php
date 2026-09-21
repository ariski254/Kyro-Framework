<?php

namespace Core;

class App {
    private static ?self $instance = null;
    public string $version = '1.0.0';

    public function __construct() {
        self::$instance = $this;
        $this->bootstrap();
    }

    public static function getInstance(): self {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function bootstrap(): void {
        // Load Environment Variables
        Env::load();

        // Load Configurations
        Config::load();

        // Register Exception and Error Handler
        ExceptionHandler::register();

        // Set default timezone
        date_default_timezone_set(Config::get('app.timezone', 'UTC'));
    }

    public function run(): void {
        // Start Session for web requests
        if (php_sapi_name() !== 'cli') {
            Session::start();
        }

        // Load Routes
        $this->loadRoutes();

        // Dispatch Request
        Router::dispatch();
    }

    protected function loadRoutes(): void {
        $baseDir = dirname(__DIR__);

        $webRoutes = $baseDir . '/routes/web.php';
        if (file_exists($webRoutes)) {
            require $webRoutes;
        }

        $apiRoutes = $baseDir . '/routes/api.php';
        if (file_exists($apiRoutes)) {
            Router::group(['prefix' => '/api'], function () use ($apiRoutes) {
                require $apiRoutes;
            });
        }
    }
}