<?php

namespace Core;

class Session {
    private static bool $started = false;

    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        if (!headers_sent()) {
            $lifetime = (int)Config::get('session.lifetime', 120) * 60;
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => Config::get('session.path', '/'),
                'domain' => Config::get('session.domain', ''),
                'secure' => (bool)Config::get('session.secure', false),
                'httponly' => (bool)Config::get('session.http_only', true),
                'samesite' => Config::get('session.same_site', 'Lax')
            ]);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        self::$started = true;
        self::ageFlash();
    }

    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? self::getFlash($key, $default);
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]) || isset($_SESSION['_flash']['old'][$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
        unset($_SESSION['_flash']['old'][$key]);
        unset($_SESSION['_flash']['new'][$key]);
    }

    public static function flash(string $key, $value): void {
        self::start();
        $_SESSION['_flash']['new'][$key] = $value;
    }

    public static function getFlash(string $key, $default = null) {
        self::start();
        return $_SESSION['_flash']['old'][$key] ?? $default;
    }

    private static function ageFlash(): void {
        if (isset($_SESSION['_flash']['old'])) {
            unset($_SESSION['_flash']['old']);
        }

        if (isset($_SESSION['_flash']['new'])) {
            $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'];
            unset($_SESSION['_flash']['new']);
        }
    }

    public static function destroy(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            @session_destroy();
            self::$started = false;
        }
    }
}
