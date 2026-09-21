<?php

use Core\Env;

return [
    'lifetime' => Env::get('SESSION_LIFETIME', 120),
    'cookie' => 'kyro_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'http_only' => true,
    'same_site' => 'lax',
];
