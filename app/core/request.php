<?php

namespace App\Core;

final class URL {
    public string $path;
    /** @var array<string, string> $query */
    public array $query;

    public function __construct(string $url) {
        $parsed = parse_url($url);
        $this->path = $parsed["path"] ? $parsed["path"] : '/';
        $this->query = $this->parse_query($parsed["query"]);
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
    public URL $url;
    public HTTPMethod $method = HTTPMethod::NONE;
    /** @var array<string, string> $form */
    public array $form = [];
    /** @var array<string, string> $headers */
    public array $headers = [];
    public bool $htmx = false;

    /** @param array<string, string> $form */
    public function __construct(string $url, HTTPMethod $method) {
        $this->url = new URL($url);
        $this->method = $method;
        $this->form = match ($method) {
            HTTPMethod::POST => $_POST,
            HTTPMethod::GET => $_GET,
        };
        $this->headers = getallheaders();
        $this->htmx = isset($this->headers['HX-Request']);
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
