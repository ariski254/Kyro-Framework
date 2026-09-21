<?php

namespace Core\Middlewares;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;

class CorsMiddleware implements Middleware {
    public function handle(Request $request, Closure $next) {
        if ($request->method() === 'OPTIONS') {
            return Response::make('', 204, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            ]);
        }

        $response = $next($request);

        if ($response instanceof Response) {
            $response->header('Access-Control-Allow-Origin', '*')
                     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                     ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }

        return $response;
    }
}
