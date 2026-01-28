<?php

namespace App\Core;

final class URL {
    public string $path;
    /** @var array<string, string> $query */
    public array $query;

    public function __construct(string $url) {
        $parsed = parse_url($url);
        $this->path = $parsed["path"] ? $parsed["path"] : '/';
        $this->query = $this->parse_query($parsed["query"] ?? "");
    }

    /**
     * @return array<string, string>
     */
    public function split_path(): array {
        return array_slice(explode('/', $this->path), 1);
    }

    /**
     * @return array<string, string>
     */
    private function  parse_query(?string $query_str): array {
        if ($query_str == "") return [];
        $kvs = explode('&', $query_str);
        $query = array();
        foreach ($kvs as $kv) {
            [0 => $key, 1 => $value] = explode('=', $kv);
            $query[$key] = $value;
        }
        return $query;
    }
}

enum HTTPMethod : string {
    case NONE   = '';
    case GET    = 'GET';
    case POST   = 'POST';
    case PUT    = 'PUT';
    case PATCH  = 'PATCH';
    case DELETE = 'DELETE';
}

final class Request {
    public URL $url;
    public HTTPMethod $method;

    public function __construct(string $url, HTTPMethod $method) {
        $this->url = new URL($url);
        $this->method = $method;
    }

    public static function current(): self {
        return new Request(
            $_SERVER['REQUEST_URI'],
            HTTPMethod::tryFrom($_SERVER['REQUEST_METHOD']) ?? HTTPMethod::NONE,
        );
    }

    public function match(string $path, HTTPMethod $method): bool {
        return $this->url->path == $path && $this->method == $method;
    }
}

function validate_path(string $p): bool {
    if ($p == '/') return true;
    return $p[0] == '/' && $p[-1] != '/';
}

final class Router {

    private Request $request;
    private bool $handled = false;
    /** @var Closure(Request): void $handler */
    private \Closure $handler;

    public function __construct() {
        $this->request = Request::current();
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

    public function group(string $group_path): RouteGroup {
        return new RouteGroup($group_path, $this);
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
        $handler($this->request);

        return true;
    }

}

final class RouteGroup {

    private Router $router;
    private string $group_path;

    public function __construct(string $group_path, Router $router) {
        assert(validate_path($group_path), 'дэбил');
        $this->group_path = $group_path == '/' ? '' : $group_path;
        $this->router = $router;
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
