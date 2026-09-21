<?php

namespace Core;

class Config {
    private static array $configs = [];
    private static bool $loaded = false;

    public static function load(): void {
        if (self::$loaded) {
            return;
        }

        $configDir = dirname(__DIR__) . '/config';
        if (is_dir($configDir)) {
            foreach (glob($configDir . '/*.php') as $file) {
                $name = basename($file, '.php');
                self::$configs[$name] = require $file;
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, $default = null) {
        self::load();

        $parts = explode('.', $key);
        $current = self::$configs;

        foreach ($parts as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return $default;
            }
            $current = $current[$part];
        }

        return $current;
    }

    public static function set(string $key, $value): void {
        self::load();

        $parts = explode('.', $key);
        $current = &self::$configs;

        foreach ($parts as $part) {
            if (!isset($current[$part]) || !is_array($current[$part])) {
                $current[$part] = [];
            }
            $current = &$current[$part];
        }

        $current = $value;
    }

    public static function all(): array {
        self::load();
        return self::$configs;
    }
}
