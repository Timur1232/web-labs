<?php

namespace App\Core\Route;

use App\Core\Helpers\Error;
use App\Core\View\Component;
use Closure;

final class Router {

    /*
     * @param ?((Closure(Request):Component)|Component) $handler
     */
    public function __construct(
        private Request $request,
        private bool $handled = false,
        private mixed $handler = null,
    ) { }

    public static function default(): self {
        return new self(
            Request::current(),
            false,
            null,
        );
    }

    public static function validate_path(string $p): bool {
        if ($p == '/') return true;
        return $p[0] == '/' && $p[-1] != '/';
    }

    /*
     * @param ((Closure(Request): Component)|Component) $handler
     */
    public function handle_rule(string $template_path, mixed $handler, HTTPMethod $method): bool {
        Error::assert(self::validate_path($template_path), "Router::handle_rule: invalid path {$template_path} - дэбил");
        if (!$this->handled && $this->request->match($template_path, $method)) {
            $this->handler = $handler;
            $this->handled = true;
            $this->request->bind_values($template_path);
        }
        return $this->handled;
    }

    public function group(string $group_path): RouteGroup {
        return RouteGroup::new($group_path, $this);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     */
    public function GET(string $path, mixed $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::GET);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     */
    public function POST(string $path, mixed $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::POST);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     */
    public function PUT(string $path, mixed $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PUT);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     */
    public function PATCH(string $path, mixed $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PATCH);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     */
    public function DELETE(string $path, mixed $handler): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::DELETE);
    }

    public function dispatch(): bool {
        if (!$this->handled) {
            if ($this->request->method == HTTPMethod::NONE) {
                Error::method_not_allowed();
            } else {
                Error::not_found($this->request->url->path);
            }
            return false;
        }

        $handler = $this->handler;
        Error::assert(isset($handler), 'no handler function - дэбил');
        if ($handler instanceof Component) {
            echo $handler->render();
        } else {
            $comp = $handler($this->request);
            echo $comp->render();
        }

        return true;
    }
}
