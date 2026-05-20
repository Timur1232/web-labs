<?php namespace App\Core\Context;

use App\Core\View\Component;
use App\Core\View\JsonComponent;
use App\Core\View\JsonSerialization;
use App\Core\View\View;

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
    * @param array<string,mixed>  $additional
    */
    public function __construct(
        public URL        $url,
        public HTTPMethod $method     = HTTPMethod::NONE,
        public array      $form       = [],
        public array      $form_files = [],
        public array      $headers    = [],
        public bool       $htmx       = false,
        public array      $binds      = [],
        public array      $additional = [],
    ) { }

    public static function current(): self {
        $method = HTTPMethod::tryFrom($_SERVER['REQUEST_METHOD']) ?? HTTPMethod::NONE;
        $headers = getallheaders();
        return new self(
            url: URL::from($_SERVER['REQUEST_URI']),
            method: $method,
            form: match ($method) {
                HTTPMethod::POST => $_POST,
                HTTPMethod::GET  => $_GET,
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

final class Response {
    /**
     * @param array<string,string> $headers
     */
    public function __construct(
        private Component $component,
        private int $status_code = 200,
        private array $headers = [],
    ) {}

    public function header(string $key, string $value): self {
        $this->headers[$key] = $value;
        return $this;
    }

    public function code(int $code): self {
        $this->status_code = $code;
        return $this;
    }

    public static function view(Component $comp, int $code = 200): self {
        return new self(
            component: $comp,
            status_code: $code,
        );
    }

    public static function text(string $text, int $code = 200): self {
        return new self(
            component: View::string($text),
            status_code: $code,
        )->header('Content-Type', 'text/plain');
    }

    public static function json(mixed $obj, int $code = 200): self {
        return new self(
            component: new JsonComponent($obj),
            status_code: $code,
        )->header('Content-Type', 'application/json; charset=utf-8');
    }

    public static function redirect(string $url, int $code = 303): self {
        return new self(
            component: View::empty(),
            status_code: $code,
        )->header('Location', $url);
    }

    public function apply(): Component {
        foreach ($this->headers as $k => $v) {
            header("{$k}: {$v}");
        }
        http_response_code($this->status_code);
        return $this->component;
    }
}
