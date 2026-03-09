<?php

namespace App\Core\Route;

final class Request {
    /*
    * @param array<string, string> $form
    * @param array<string, string> $headers
    * @param array<string,mixed> $binds
    */
    public function __construct(
        public URL $url,
        public HTTPMethod $method = HTTPMethod::NONE,
        public array $form = [],
        public array $headers = [],
        public bool $htmx = false,
        public array $binds = [],
    ) { }

    public static function current(): self {
        $method = HTTPMethod::tryFrom($_SERVER['REQUEST_METHOD']) ?? HTTPMethod::NONE;
        $headers = getallheaders();
        return new self(
            URL::from($_SERVER['REQUEST_URI']),
            $method,
            match ($method) {
                HTTPMethod::POST => $_POST,
                HTTPMethod::GET => $_GET,
            },
            $headers,
            isset($headers['HX-Request']),
        );
    }

    public function match(string $template_path, HTTPMethod $method): bool {
        return $this->url->match($template_path) && $this->method == $method;
    }

    public function bind_values(string $template_path): void {
        $this->binds = $this->url->bind_values($template_path);
    }
}
