<?php

namespace Core;

use Throwable;

class ExceptionHandler {
    public static function register(): void {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleError(int $severity, string $message, string $file, int $line): void {
        if (!(error_reporting() & $severity)) {
            return;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(Throwable $e): void {
        $debug = Config::get('app.debug', true);

        if (php_sapi_name() === 'cli') {
            echo "\n\033[41;37m Exception: " . $e->getMessage() . " \033[0m\n";
            echo "In \033[33m" . $e->getFile() . "\033[0m on line \033[32m" . $e->getLine() . "\033[0m\n\n";
            echo $e->getTraceAsString() . "\n\n";
            return;
        }

        http_response_code(500);

        if (Request::isJson()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $debug ? $e->getFile() : null,
                'line' => $debug ? $e->getLine() : null,
                'trace' => $debug ? explode("\n", $e->getTraceAsString()) : null,
            ]);
            return;
        }

        if ($debug) {
            self::renderDebugPage($e);
        } else {
            self::renderProductionErrorPage();
        }
    }

    private static function renderDebugPage(Throwable $e): void {
        $file = $e->getFile();
        $line = $e->getLine();
        $lines = [];

        if (file_exists($file) && is_readable($file)) {
            $fileContent = file($file);
            $start = max(0, $line - 7);
            $end = min(count($fileContent) - 1, $line + 7);

            for ($i = $start; $i <= $end; $i++) {
                $lines[$i + 1] = $fileContent[$i];
            }
        }

        $trace = $e->getTrace();
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $exceptionClass = get_class($e);

        ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyro Error: <?= $message ?></title>
    <style>
        :root {
            --bg: #090d16;
            --surface: #111827;
            --surface-border: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-border: rgba(239, 68, 68, 0.3);
            --accent: #6366f1;
            --code-bg: #030712;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            padding: 2rem 1.5rem;
        }
        .container { max-width: 1100px; margin: 0 auto; }
        .badge-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .badge {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: #f87171;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
        }
        .framework-name {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }
        .header {
            background: var(--surface);
            border: 1px solid var(--surface-border);
            border-left: 5px solid var(--danger);
            border-radius: 12px;
            padding: 1.75rem;
            margin-bottom: 1.75rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4);
        }
        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }
        .file-location {
            color: var(--text-muted);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.95rem;
        }
        .file-location span { color: #60a5fa; font-weight: bold; }
        .card {
            background: var(--surface);
            border: 1px solid var(--surface-border);
            border-radius: 12px;
            margin-bottom: 1.75rem;
            overflow: hidden;
        }
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--surface-border);
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.02);
        }
        .code-view {
            background: var(--code-bg);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.9rem;
            padding: 1rem 0;
            overflow-x: auto;
        }
        .code-line {
            display: flex;
            padding: 0.15rem 1rem;
            line-height: 1.5;
        }
        .code-line.highlight {
            background: rgba(239, 68, 68, 0.22);
            border-left: 3px solid var(--danger);
        }
        .line-number {
            width: 45px;
            color: #4b5563;
            text-align: right;
            padding-right: 1.25rem;
            user-select: none;
            flex-shrink: 0;
        }
        .highlight .line-number { color: #fca5a5; font-weight: bold; }
        .line-code {
            color: #d1d5db;
            white-space: pre;
        }
        .trace-item {
            padding: 0.85rem 1.5rem;
            border-bottom: 1px solid var(--surface-border);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.85rem;
            display: flex;
            gap: 1rem;
        }
        .trace-item:last-child { border-bottom: none; }
        .trace-num { color: #6b7280; width: 25px; flex-shrink: 0; text-align: right; }
        .trace-call { color: #38bdf8; font-weight: 600; }
        .trace-file { color: var(--text-muted); margin-top: 0.2rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge-header">
            <span class="badge"><?= htmlspecialchars($exceptionClass) ?></span>
            <span class="framework-name">Kyro Framework Debugger</span>
        </div>

        <div class="header">
            <h1><?= $message ?></h1>
            <div class="file-location">
                di <?= htmlspecialchars($file) ?> pada baris <span><?= $line ?></span>
            </div>
        </div>

        <?php if (!empty($lines)): ?>
        <div class="card">
            <div class="card-header">
                <span>Cuplikan Kode</span>
                <span style="color: var(--text-muted); font-size: 0.8rem;"><?= htmlspecialchars(basename($file)) ?>:<?= $line ?></span>
            </div>
            <div class="code-view">
                <?php foreach ($lines as $num => $content): ?>
                    <div class="code-line <?= $num === $line ? 'highlight' : '' ?>">
                        <span class="line-number"><?= $num ?></span>
                        <span class="line-code"><?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Stack Trace</div>
            <div>
                <?php foreach ($trace as $idx => $t): ?>
                    <div class="trace-item">
                        <span class="trace-num">#<?= $idx ?></span>
                        <div>
                            <div class="trace-call">
                                <?= htmlspecialchars(($t['class'] ?? '') . ($t['type'] ?? '') . ($t['function'] ?? '')) ?>()
                            </div>
                            <?php if (isset($t['file'])): ?>
                                <div class="trace-file"><?= htmlspecialchars($t['file']) ?> : <?= $t['line'] ?? '?' ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
        <?php
    }

    private static function renderProductionErrorPage(): void {
        ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .box {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 3rem 2.5rem;
            max-width: 450px;
        }
        h1 { font-size: 3.5rem; color: #f85149; margin-bottom: 0.5rem; }
        p { color: #8b949e; margin-bottom: 1.5rem; }
        a {
            display: inline-block;
            background: #238636;
            color: white;
            text-decoration: none;
            padding: 0.6rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>500</h1>
        <h2>Terjadi Kesalahan Server</h2>
        <p>Maaf, permintaan Anda tidak dapat diproses saat ini. Silakan coba kembali beberapa saat lagi.</p>
        <a href="/">Kembali ke Beranda</a>
    </div>
</body>
</html>
        <?php
    }
}
