<?php

/**
 * @return array<string>
 */
function split_path(): array {
    $sp = explode('/', $_SERVER['REQUEST_URI']);
    return array(
        'controller' => $sp[1],
        'action'     => $sp[2],
        ...array_slice($sp, 3),
    );
}

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

final class Router {

    /*
     * @var array<Controller> $controllers
     */
    public array $controllers;

    /**
     * @param array<Controller> $controllers
     */
    public function __construct(array $controllers) {
        $this->controllers = $controllers;
    }

    public function route(): void {
        $path = split_path();

        $controller_name = $path['controller'] ? $path['controller'] : 'index';
        $action_name = $path['action'] ? $path['action'] : 'get';

        if ($this->controllers[$controller_name] == null) {
            http_response_code(404);
            die();
        }

        $controller = $this->controllers[$controller_name];

        if(method_exists($controller, $action_name)) {
            $controller->$action_name();
        } else {
            http_response_code(404);
            die();
        }
    }
}

