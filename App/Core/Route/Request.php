<?php

namespace App\Core\Route;

final class Request {
    /*
    * @param array<string, string> $form
    * @param array<string, string> $headers
    */
    public function __construct(
        public URL $url,
        public HTTPMethod $method = HTTPMethod::NONE,
        public array $form = [],
        public array $headers = [],
        public bool $htmx = false,
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

    public function match(string $path, HTTPMethod $method): bool {
        return $this->url->path == $path && $this->method == $method;
    }
}
