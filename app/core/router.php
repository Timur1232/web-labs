<?php

namespace App\Core;
require_once 'app/core/request.php';

function validate_path(string $p): bool {
    if ($p == '/') return true;
    return $p[0] == '/' && $p[-1] != '/';
}

final class Router {

    /** @param ?Closure(Request): void $handler */
    private function __construct(
        private Request $request,
        private bool $handled = false,
        private ?\Closure $handler,
    ) { }

    public static function default(): self {
        return new self(
            Request::current(),
            false,
            null,
        );
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function handle_rule(string $path, \Closure $handler, HTTPMethod $method): bool {
        assert(validate_path($path), 'дэбил');
        if (!$this->handled && $this->request->match($path, $method)) {
            $this->handler = $handler;
            $this->handled = true;
        }
        return $this->handled;
    }

    public function group(string $group_path): RouteGroup {
        return RouteGroup::new($group_path, $this);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function GET(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::GET);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function POST(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::POST);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function PUT(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PUT);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function PATCH(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PATCH);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function DELETE(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::DELETE);
    }

    public function dispatch(): bool {
        if (!$this->handled) {
            if ($this->request->method == HTTPMethod::NONE) {
                http_response_code(405);
                echo '<h1>405 Method Not Allowed</h1>';
                echo '<a href="/">Home</a>';
            } else {
                $path = $this->request->url->path;
                http_response_code(404);
                echo '<h1>404 Not Found</h1>';
                echo "<p>$path не найден</p>";
                echo '<a href="/">Home</a>';
            }
            return false;
        }

        $handler = $this->handler;
        assert(isset($handler), 'дэбил');
        $handler($this->request);

        return true;
    }

}

final class RouteGroup {

    private function __construct(
        private Router $router,
        private string $group_path,
    ) { }

    public static function new(string $group_path, Router $router): self {
        assert(validate_path($group_path), 'дэбил');
        return new self(
            $router,
            $group_path == '/' ? '' : $group_path,
        );
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function handle_rule(string $path, \Closure $handler, HTTPMethod $method): bool {
        assert(validate_path($path), 'дэбил');
        $full_path = $this->group_path . ($path == '/' ? '' : $path);
        $full_path = $full_path == '' ? '/' : $full_path;
        return $this->router->handle_rule($full_path, $handler, $method);
    }

    public function group(string $group_path): self {
        return new RouteGroup($this->group_path . $group_path, $this->router);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function GET(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::GET);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function POST(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::POST);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function PUT(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PUT);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function PATCH(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PATCH);
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function DELETE(string $path, \Closure $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::DELETE);
    }
}
