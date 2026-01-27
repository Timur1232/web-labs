<?php

namespace App\Core;

/**
 * @return array<string>
 */
function split_path(string $path): array {
    $sp = \explode('/', $path);
    return array(
        'controller' => $sp[1],
        'action'     => $sp[2],
        ...\array_slice($sp, 3),
    );
}

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

enum HTTPMethod : string {
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
}

final class RouteRule {

    public string $path;
    /**
     * @var array{class-stirng, string} $handler
     */
    public array $handler;
    public HTTPMethod $method;

    /**
     * @param array{class-stirng, string} $handler
     */
    public function __construct(string $path, array $handler, HTTPMethod $method) {
        $this->path = $path;
        $this->handler = $handler;
        $this->method = $method;
    }

}

final class Router {

    /*
     * @var array<RouteRule> $controllers
     */
    public array $rules;

    /**
     * @param array{class-string, string} $handler
     */
    public function add(string $path, array $handler, HTTPMethod $method): void {
        \assert(\class_exists($handler[0]), 'дэбил');
        \assert(\method_exists($handler[0], $handler[1]), 'дэбил');
        $this->rules[] = new RouteRule($path, $handler, $method);
    }

    /**
     * @param array{class-string, string} $handler
     */
    public function GET(string $path, array $handler): void {
        $this->add($path, $handler, HTTPMethod::GET);
    }

    /**
     * @param array{class-string, string} $handler
     */
    public function POST(string $path, array $handler): void {
        $this->add($path, $handler, HTTPMethod::POST);
    }

    /**
     * @param array{class-string, string} $handler
     */
    public function PUT(string $path, array $handler): void {
        $this->add($path, $handler, HTTPMethod::PUT);
    }

    /**
     * @param array{class-string, string} $handler
     */
    public function PATCH(string $path, array $handler): void {
        $this->add($path, $handler, HTTPMethod::PATCH);
    }

    /**
     * @param array{class-string, string} $handler
     */
    public function DELETE(string $path, array $handler): void {
        $this->add($path, $handler, HTTPMethod::DELETE);
    }

    public function route(string $path): void {
        $request_method = HTTPMethod::tryFrom($_SERVER['REQUEST_METHOD']);
        if ($request_method == null) {
            http_response_code(405);
            echo '<p>405 Method not allowed</p>';
            die();
        }

        $path = explode('?', $path)[0];

        $rule = null;
        foreach ($this->rules as $r) {
            if ($r->method == $request_method && $r->path == $path) {
                $rule = $r;
                break;
            }
        }

        if ($rule == null) {
            http_response_code(307);
            header('Location: /');
            die();
        }

        $handler_class = new $rule->handler[0]();
        $handler_method = $rule->handler[1];
        $handler_class->$handler_method();
    }

}

