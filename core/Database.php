<?php

namespace Core;

use PDO;
use PDOException;

class Database {
    private static array $connections = [];

    public static function connect(?string $name = null): PDO {
        $default = Config::get('database.default', 'mysql');
        $name = $name ?? $default;

        if (isset(self::$connections[$name])) {
            return self::$connections[$name];
        }

        $config = Config::get("database.connections.{$name}");
        if (!$config) {
            throw new \Exception("Koneksi database [{$name}] belum dikonfigurasi.");
        }

        $driver = $config['driver'] ?? 'mysql';

        try {
            if ($driver === 'sqlite') {
                $dbPath = $config['database'];
                if (!file_exists($dbPath) && $dbPath !== ':memory:') {
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    touch($dbPath);
                }
                $dsn = "sqlite:{$dbPath}";
                $pdo = new PDO($dsn);
            } elseif ($driver === 'pgsql') {
                $host = $config['host'];
                $port = $config['port'] ?? 5432;
                $db = $config['database'];
                $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
                $pdo = new PDO($dsn, $config['username'], $config['password']);
            } else {
                // Default MySQL
                $host = $config['host'];
                $port = $config['port'] ?? 3306;
                $db = $config['database'];
                $charset = $config['charset'] ?? 'utf8mb4';
                $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
                $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$connections[$name] = $pdo;
            return $pdo;
        } catch (PDOException $e) {
            throw new \Exception("Gagal menghubungkan ke database [{$name}]: " . $e->getMessage(), 0, $e);
        }
    }

    public static function pdo(): PDO {
        return self::connect();
    }

    public static function raw(string $query, array $params = []): array {
        $stmt = self::connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function statement(string $query, array $params = []): bool {
        $stmt = self::connect()->prepare($query);
        return $stmt->execute($params);
    }

    public static function lastInsertId(): string {
        return self::connect()->lastInsertId();
    }

    public static function beginTransaction(): bool {
        return self::connect()->beginTransaction();
    }

    public static function commit(): bool {
        return self::connect()->commit();
    }

    public static function rollBack(): bool {
        return self::connect()->rollBack();
    }

    public static function createDatabaseIfNotExists(?string $name = null): bool {
        $default = Config::get('database.default', 'mysql');
        $name = $name ?? $default;

        $config = Config::get("database.connections.{$name}");
        if (!$config || ($config['driver'] ?? 'mysql') !== 'mysql') {
            return false;
        }

        $host = $config['host'];
        $port = $config['port'] ?? 3306;
        $db = $config['database'];
        $user = $config['username'];
        $pass = $config['password'];

        $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return true;
    }
}