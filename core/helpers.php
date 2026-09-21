<?php

use Core\App;
use Core\Config;
use Core\Csrf;
use Core\Env;
use Core\Request;
use Core\Response;
use Core\Router;
use Core\Session;
use Core\View;

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        return Config::get($key, $default);
    }
}

if (!function_exists('app')) {
    function app(): App {
        return App::getInstance();
    }
}

if (!function_exists('request')) {
    function request(?string $key = null, $default = null) {
        $req = Request::capture();
        if ($key === null) {
            return $req;
        }
        return $req->input($key, $default);
    }
}

if (!function_exists('response')) {
    function response(string $content = '', int $status = 200, array $headers = []): Response {
        return Response::make($content, $status, $headers);
    }
}

if (!function_exists('json')) {
    function json($data, int $status = 200, array $headers = []): Response {
        return Response::json($data, $status, $headers);
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [], int $status = 200): Response {
        return Response::view($view, $data, $status);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302): Response {
        return Response::redirect($url, $status);
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, $default = null) {
        if ($key === null) {
            return new class {
                public function get($key, $default = null) { return Session::get($key, $default); }
                public function set($key, $val) { Session::set($key, $val); }
                public function has($key) { return Session::has($key); }
                public function flash($key, $val) { Session::flash($key, $val); }
            };
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return Csrf::field();
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string {
        return Router::url($name, $params);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $base = rtrim(Config::get('app.url', 'http://localhost:8000'), '/');
        $path = ltrim($path, '/');
        return $path ? "{$base}/{$path}" : $base;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url(ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dump')) {
    function dump(...$vars): void {
        foreach ($vars as $var) {
            if (php_sapi_name() === 'cli') {
                var_dump($var);
            } else {
                echo '<pre style="background:#0f172a;color:#38bdf8;padding:1rem;border-radius:8px;font-family:monospace;margin:0.5rem 0;border:1px solid #1e293b;">';
                var_dump($var);
                echo '</pre>';
            }
        }
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void {
        dump(...$vars);
        exit(1);
    }
}
