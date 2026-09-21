<?php

namespace Core;

class Response {
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = []) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function make(string $content = '', int $statusCode = 200, array $headers = []): self {
        return new self($content, $statusCode, $headers);
    }

    public function status(int $code): self {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }

    public function content(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getContent(): string {
        return $this->content;
    }

    public static function json($data, int $statusCode = 200, array $headers = []): self {
        $headers['Content-Type'] = 'application/json';
        $encoded = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return new self($encoded ?: '{}', $statusCode, $headers);
    }

    public static function view(string $view, array $data = [], int $statusCode = 200): self {
        $content = View::render($view, $data);
        return new self($content, $statusCode, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function redirect(string $url, int $statusCode = 302): self {
        return new self('', $statusCode, ['Location' => $url]);
    }

    public static function download(string $filePath, ?string $fileName = null): void {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "File not found.";
            return;
        }

        $fileName = $fileName ?? basename($filePath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function send(): void {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }
}