<?php

namespace App\Core\Route;

use App\Core\Helpers\Error;

final class RouteGroup {

    private function __construct(
        private Router $router,
        private string $group_path,
    ) { }

    public static function new(string $group_path, Router $router): self {
        Error::assert(Router::validate_path($group_path), "invalid group path {$group_path} - дэбил");
        return new self(
            $router,
            $group_path == '/' ? '' : $group_path,
        );
    }

    /**
     * @param Closure(Request): void $handler
     */
    public function handle_rule(string $path, \Closure $handler, HTTPMethod $method): bool {
        Error::assert(Router::validate_path($path), "invalid path {$path} - дэбил");
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
