<?php

namespace Core\Middlewares;

use Closure;
use Core\Csrf;
use Core\Middleware;
use Core\Request;
use Core\Response;

class CsrfMiddleware implements Middleware {
    public function handle(Request $request, Closure $next) {
        $readingMethods = ['GET', 'HEAD', 'OPTIONS'];

        if (!in_array($request->method(), $readingMethods)) {
            // Check header or input for token
            $token = $request->input('_csrf_token') ?? $request->header('X-CSRF-TOKEN');

            if (!Csrf::validate($token)) {
                if ($request->isJson()) {
                    return Response::json(['error' => 'CSRF token mismatch or expired.'], 419);
                }

                return Response::make('<h1>419 - Page Expired</h1><p>Token CSRF tidak valid atau sesi telah kedaluwarsa. Silakan muat ulang halaman.</p>', 419, [
                    'Content-Type' => 'text/html; charset=UTF-8'
                ]);
            }
        }

        return $next($request);
    }
}
