<?php namespace App\Core\Route;
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Core\View\Component;
use App\Core\Middleware\Middleware;
use Closure;

enum HTTPMethod : string {
    case NONE   = '';
    case GET    = 'GET';
    case POST   = 'POST';
    case PUT    = 'PUT';
    case PATCH  = 'PATCH';
    case DELETE = 'DELETE';
}

final class Request {
    /*
    * @param array<string,string> $form
    * @param array<string,string> $form_files
    * @param array<string,string> $headers
    * @param array<string,string> $binds
    */
    public function __construct(
        public URL        $url,
        public HTTPMethod $method     = HTTPMethod::NONE,
        public array      $form       = [],
        public array      $form_files = [],
        public array      $headers    = [],
        public bool       $htmx       = false,
        public array      $binds      = [],
    ) { }

    public static function current(): self {
        $method = HTTPMethod::tryFrom($_SERVER['REQUEST_METHOD']) ?? HTTPMethod::NONE;
        $headers = getallheaders();
        return new self(
            url: URL::from($_SERVER['REQUEST_URI']),
            method: $method,
            form: match ($method) {
                HTTPMethod::POST => $_POST,
                HTTPMethod::GET => $_GET,
            },
            form_files: $_FILES,
            headers: $headers,
            htmx: isset($headers['HX-Request']),
        );
    }

    public function match(string $template_path, HTTPMethod $method): bool {
        return $this->url->match($template_path) && $this->method == $method;
    }

    public function bind_values(string $template_path): void {
        $this->binds = $this->url->bind_values($template_path);
    }
}

final class Router {
    /*
     * @param ?((Closure(Request):Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function __construct(
        private Request $request,
        private bool    $handled    = false,
        private mixed   $handler    = null,
        private array   $middleware = [],
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
     * @param array<int,Middleware|class-string> $middleware
     */
    public function handle_rule(string $template_path, mixed $handler, HTTPMethod $method, array $middleware = []): bool {
        Error::assert(self::validate_path($template_path), "Router::handle_rule: invalid path {$template_path} - дэбил");
        if (!$this->handled && $this->request->match($template_path, $method)) {
            $this->handler = $handler;
            $this->middleware = $middleware;
            $this->handled = true;
            $this->request->bind_values($template_path);
        }
        return $this->handled;
    }

    /**
     * @param array<int,Middleware|class-string> $middleware
     */
    public function group(string $group_path, array $middleware = []): RouteGroup {
        return RouteGroup::new($group_path, $this, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function GET(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::GET, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function POST(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::POST, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function PUT(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PUT, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function PATCH(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PATCH, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function DELETE(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::DELETE, middleware: $middleware);
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
            if (count($this->middleware) !== 0) {
                foreach (array_reverse($this->middleware) as $mw) {
                    if (is_string($mw)) {
                        $mw = new $mw;
                    }
                    $handler = $mw->apply($this->request, $handler);
                }
            }
            $comp = $handler($this->request);
            echo $comp->render();
        }

        return true;
    }
}

final class RouteGroup {
    /**
     * @param array<int,Middleware|class-string> $middleware
     */
    public function __construct(
        private Router $router,
        private string $group_path,
        private array $middleware = [],
    ) { }
    /**
     * @param array<int,Middleware|class-string> $middleware
     */
    public static function new(string $group_path, Router $router, array $middleware = []): self {
        Error::assert(Router::validate_path($group_path), "invalid group path {$group_path} - дэбил");
        return new self(
            router: $router,
            group_path: $group_path == '/' ? '' : $group_path,
            middleware: $middleware,
        );
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function handle_rule(string $path, mixed $handler, HTTPMethod $method, array $middleware = []): bool {
        $full_path = $this->group_path . ($path == '/' ? '' : $path);
        $full_path = $full_path == '' ? '/' : $full_path;
        return $this->router->handle_rule($full_path, $handler, $method, array_merge($this->middleware, $middleware));
    }
    /**
     * @param array<int,Middleware|class-string> $middleware
     */
    public function group(string $group_path, array $middleware = []): self {
        return new RouteGroup(group_path: $this->group_path . $group_path, router: $this->router, middleware: array_merge($this->middleware, $middleware));
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function GET(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::GET, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function POST(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::POST, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function PUT(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PUT, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function PATCH(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::PATCH, middleware: $middleware);
    }

    /**
     * @param ((Closure(Request): Component)|Component) $handler
     * @param array<int,Middleware|class-string> $middleware
     */
    public function DELETE(string $path, mixed $handler, array $middleware = []): bool {
        return $this->handle_rule($path, $handler, HTTPMethod::DELETE, middleware: $middleware);
    }
}
