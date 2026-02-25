<?php

namespace App\Core;

final class URL {
    /** @param array<string, string> $query */
    public function __construct(
        public string $path,
        public array $query,
    ) { }

    public static function from(string $url): self {
        $parsed = parse_url($url);
        return new self(
            $parsed["path"] ? $parsed["path"] : '/',
            self::parse_query($parsed["query"]),
        );
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
    public static function parse_query(?string $query_str): array {
        if (!isset($query_str) || !$query_str) return [];
        $kvs = explode('&', $query_str);
        $query = [];
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
