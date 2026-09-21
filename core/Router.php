<?php

namespace Core;

use Closure;

class Router {
    protected static array $routes = [];
    protected static array $namedRoutes = [];
    protected static array $groupStack = [];

    public static function get(string $uri, $action): Route {
        return self::addRoute(['GET'], $uri, $action);
    }

    public static function post(string $uri, $action): Route {
        return self::addRoute(['POST'], $uri, $action);
    }

    public static function put(string $uri, $action): Route {
        return self::addRoute(['PUT'], $uri, $action);
    }

    public static function patch(string $uri, $action): Route {
        return self::addRoute(['PATCH'], $uri, $action);
    }

    public static function delete(string $uri, $action): Route {
        return self::addRoute(['DELETE'], $uri, $action);
    }

    public static function options(string $uri, $action): Route {
        return self::addRoute(['OPTIONS'], $uri, $action);
    }

    public static function any(string $uri, $action): Route {
        return self::addRoute(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);
    }

    public static function match(array $methods, string $uri, $action): Route {
        return self::addRoute($methods, $uri, $action);
    }

    public static function group(array $attributes, Closure $callback): void {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    protected static function addRoute(array $methods, string $uri, $action): Route {
        $prefix = '';
        $groupMiddlewares = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $groupMiddlewares = array_merge($groupMiddlewares, $m);
            }
        }

        $fullUri = $prefix . '/' . trim($uri, '/');
        $fullUri = '/' . trim($fullUri, '/');

        $route = new Route($methods, $fullUri, $action);

        if (!empty($groupMiddlewares)) {
            $route->middleware($groupMiddlewares);
        }

        self::$routes[] = $route;
        return $route;
    }

    public static function registerNamedRoute(string $name, Route $route): void {
        self::$namedRoutes[$name] = $route;
    }

    public static function url(string $name, array $params = []): string {
        if (!isset(self::$namedRoutes[$name])) {
            throw new \Exception("Route dengan nama [{$name}] tidak terdaftar.");
        }

        $uri = self::$namedRoutes[$name]->getUri();
        foreach ($params as $key => $val) {
            $uri = str_replace('{' . $key . '}', (string)$val, $uri);
        }

        return url($uri);
    }

    public static function getRoutes(): array {
        return self::$routes;
    }

    public static function dispatch(?Request $request = null) {
        $request = $request ?? Request::capture();
        $uri = $request->uri();
        $method = $request->method();

        $matchedRoute = null;
        $parameters = [];
        $methodNotAllowed = false;

        foreach (self::$routes as $route) {
            if ($route->matches($method, $uri, $parameters)) {
                $matchedRoute = $route;
                break;
            }

            // Check if URI matches under a different HTTP method
            $dummyParams = [];
            foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'] as $m) {
                if ($route->matches($m, $uri, $dummyParams)) {
                    $methodNotAllowed = true;
                }
            }
        }

        if (!$matchedRoute) {
            if ($methodNotAllowed) {
                http_response_code(405);
                if ($request->isJson()) {
                    return Response::json(['error' => 'Method Not Allowed'], 405)->send();
                }
                echo "<h1>405 Method Not Allowed</h1>";
                return;
            }

            http_response_code(404);
            if ($request->isJson()) {
                return Response::json(['error' => 'Endpoint tidak ditemukan.'], 404)->send();
            }

            // Try to render custom 404 view if it exists
            $custom404 = dirname(__DIR__) . '/app/Views/errors/404.php';
            if (file_exists($custom404)) {
                echo View::render('errors.404');
                return;
            }

            echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>404 Not Found</title><style>body{background:#090d16;color:#f3f4f6;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;flex-direction:column}h1{font-size:4rem;margin:0;color:#6366f1}p{color:#9ca3af}</style></head><body><h1>404</h1><p>Halaman tidak ditemukan di Kyro Framework.</p></body></html>';
            return;
        }

        // Run Route Middlewares Pipeline
        $middlewares = $matchedRoute->getMiddlewares();

        $actionClosure = function ($req) use ($matchedRoute, $parameters) {
            $action = $matchedRoute->getAction();

            if (is_callable($action)) {
                return $action(...array_values($parameters));
            }

            if (is_array($action)) {
                [$controllerClass, $methodName] = $action;
                $controller = new $controllerClass();
                return $controller->$methodName(...array_values($parameters));
            }

            if (is_string($action) && str_contains($action, '@')) {
                [$controllerClass, $methodName] = explode('@', $action);
                $fullClass = "App\\Controllers\\" . $controllerClass;
                $controller = new $fullClass();
                return $controller->$methodName(...array_values($parameters));
            }

            throw new \Exception("Action rute tidak valid.");
        };

        // Build pipeline from middlewares
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middlewareClass) {
                return function ($req) use ($next, $middlewareClass) {
                    $middlewareInstance = new $middlewareClass();
                    return $middlewareInstance->handle($req, $next);
                };
            },
            $actionClosure
        );

        $response = $pipeline($request);

        // Handle response
        if ($response instanceof Response) {
            $response->send();
        } elseif (is_array($response) || is_object($response)) {
            Response::json($response)->send();
        } elseif (is_string($response) || is_numeric($response)) {
            echo $response;
        }
    }
}