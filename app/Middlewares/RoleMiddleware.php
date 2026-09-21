<?php

namespace App\Middlewares;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;

class RoleMiddleware implements Middleware {
    public function handle(Request $request, Closure $next) {
        // Logika middleware sebelum request dieksekusi

        $response = $next($request);

        // Logika middleware setelah response diperoleh
        return $response;
    }
}
