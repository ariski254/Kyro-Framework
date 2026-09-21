<?php

namespace Core;

use Core\Exceptions\ValidationException;

class Request {
    private static ?self $instance = null;
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $json;
    private ?string $rawBody = null;

    public function __construct(array $get = [], array $post = [], array $files = [], array $server = []) {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;

        $input = file_get_contents('php://input');
        $this->rawBody = $input ?: '';
        $decoded = json_decode($this->rawBody, true);
        $this->json = is_array($decoded) ? $decoded : [];
    }

    public static function capture(): self {
        if (!self::$instance) {
            self::$instance = new self($_GET, $_POST, $_FILES, $_SERVER);
        }
        return self::$instance;
    }

    public static function instance(): self {
        return self::capture();
    }

    public function method(): string {
        $method = $this->server['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST') {
            $override = $this->post['_method'] ?? $this->server['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($override) {
                return strtoupper((string)$override);
            }
        }
        return strtoupper($method);
    }

    public function isMethod(string $method): bool {
        return $this->method() === strtoupper($method);
    }

    public function uri(): string {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return '/' . trim($path ?: '/', '/');
    }

    public function url(): string {
        $scheme = (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . $this->uri();
    }

    public function fullUrl(): string {
        $url = $this->url();
        $query = $this->server['QUERY_STRING'] ?? '';
        return $query ? $url . '?' . $query : $url;
    }

    public function ip(): string {
        return $this->server['HTTP_CF_CONNECTING_IP']
            ?? $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['REMOTE_ADDR']
            ?? '127.0.0.1';
    }

    public function input(?string $key = null, $default = null) {
        $all = $this->all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public function query(?string $key = null, $default = null) {
        if ($key === null) {
            return $this->get;
        }
        return $this->get[$key] ?? $default;
    }

    public function post(?string $key = null, $default = null) {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function json(?string $key = null, $default = null) {
        if ($key === null) {
            return $this->json;
        }
        return $this->json[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($this->get, $this->post, $this->json);
    }

    public function has(string $key): bool {
        $all = $this->all();
        return isset($all[$key]) && $all[$key] !== '';
    }

    public function header(string $key, $default = null) {
        $headerKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$headerKey] ?? $default;
    }

    public function bearerToken(): ?string {
        $header = $this->header('Authorization') ?? $this->server['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function isJson(): bool {
        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains($contentType, 'application/json') || str_contains($accept, 'application/json');
    }

    public function file(string $key) {
        return $this->files[$key] ?? null;
    }

    public function validate(array $rules): array {
        $data = $this->all();
        $errors = [];
        $validated = [];

        foreach ($rules as $field => $fieldRules) {
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $val = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $param = null;
                if (str_contains($rule, ':')) {
                    [$rule, $param] = explode(':', $rule, 2);
                }

                if ($rule === 'required' && ($val === null || $val === '')) {
                    $errors[$field][] = "Kolom {$field} wajib diisi.";
                }

                if ($val !== null && $val !== '') {
                    if ($rule === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "Kolom {$field} harus berupa alamat email yang valid.";
                    }
                    if ($rule === 'numeric' && !is_numeric($val)) {
                        $errors[$field][] = "Kolom {$field} harus berupa angka.";
                    }
                    if ($rule === 'min' && strlen((string)$val) < (int)$param) {
                        $errors[$field][] = "Kolom {$field} minimal harus {$param} karakter.";
                    }
                    if ($rule === 'max' && strlen((string)$val) > (int)$param) {
                        $errors[$field][] = "Kolom {$field} maksimal {$param} karakter.";
                    }
                }
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $val;
            }
        }

        if (!empty($errors)) {
            if ($this->isJson()) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Validasi gagal.', 'errors' => $errors]);
                exit;
            }

            Session::flash('errors', $errors);
            Session::flash('old', $data);
            Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
            exit;
        }

        return $validated;
    }

    // Static proxies for backward compatibility and clean syntax
    public static function __callStatic($method, $args) {
        $instance = self::capture();
        return $instance->$method(...$args);
    }
}