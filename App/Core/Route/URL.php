<?php

namespace App\Core\Route;

use App\Core\Helpers\Helpers;

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

    public function match(string $template_path): bool {
        $splited_path = explode('/', $this->path);
        $splited_template = explode('/', $template_path);
        if (count($splited_path) !== count($splited_template)) return false;

        foreach (Helpers::zip($splited_path, $splited_template) as [$real, $templ]) {
            if (isset($templ[0]) && $templ[0] === ':') continue;
            if ($templ !== $real) return false;
        }

        return true;
    }

    /*
     * WARNING: There are no validation in this method. To validate template use `match` method
     *
     * @return array<string,string>
     */
    public function bind_values(string $template_path): array {
        $ret = [];
        foreach (Helpers::zip(explode('/', $this->path), explode('/', $template_path)) as [$real, $templ]) {
            if (isset($templ[0]) && $templ[0] === ':') {
                $ret[substr($templ, 1)] = $real;
            }
        }
        return $ret;
    }
}
