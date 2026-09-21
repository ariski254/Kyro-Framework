<?php

namespace Core;

class Csrf {
    public static function token(): string {
        Session::start();
        if (!Session::has('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('_csrf_token');
    }

    public static function field(): string {
        $token = self::token();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool {
        if (!$token) {
            return false;
        }
        $sessionToken = Session::get('_csrf_token');
        return hash_equals((string)$sessionToken, (string)$token);
    }
}
