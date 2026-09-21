<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:2rem;background:#09090b;color:#f4f4f5;"><h2>Autoload File Missing</h2><p>Silakan jalankan <code>composer install</code> atau <code>composer dump-autoload</code> di terminal proyek terlebih dahulu.</p></body></html>';
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

use Core\App;

$app = new App();
$app->run();