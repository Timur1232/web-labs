<?php

namespace App\Core\Route;

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
