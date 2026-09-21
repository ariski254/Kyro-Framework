<?php

namespace Core;

class Route {
    protected array $methods;
    protected string $uri;
    protected $action;
    protected array $middlewares = [];
    protected ?string $name = null;
    protected ?string $regex = null;
    protected array $parameterNames = [];

    public function __construct(array $methods, string $uri, $action) {
        $this->methods = array_map('strtoupper', $methods);
        if (in_array('GET', $this->methods) && !in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

        $this->uri = '/' . trim($uri, '/');
        $this->action = $action;
        $this->compileRegex();
    }

    public function name(string $name): self {
        $this->name = $name;
        Router::registerNamedRoute($name, $this);
        return $this;
    }

    public function middleware($middleware): self {
        if (is_array($middleware)) {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        } else {
            $this->middlewares[] = $middleware;
        }
        return $this;
    }

    public function getMethods(): array {
        return $this->methods;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getAction() {
        return $this->action;
    }

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    public function getName(): ?string {
        return $this->name;
    }

    protected function compileRegex(): void {
        // Replace {param} with named capturing group (?P<param>[^/]+)
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', function ($matches) {
            $this->parameterNames[] = $matches[1];
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $this->uri);

        $this->regex = '#^' . $pattern . '$#';
    }

    public function matches(string $requestMethod, string $requestUri, array &$parameters = []): bool {
        if (!in_array($requestMethod, $this->methods) && !in_array('ANY', $this->methods)) {
            return false;
        }

        $cleanUri = '/' . trim($requestUri, '/');

        if (preg_match($this->regex, $cleanUri, $matches)) {
            $parameters = [];
            foreach ($this->parameterNames as $paramName) {
                if (isset($matches[$paramName])) {
                    $parameters[$paramName] = $matches[$paramName];
                }
            }
            return true;
        }

        return false;
    }
}
