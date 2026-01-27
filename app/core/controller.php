<?php

namespace App\Core;

require_once 'app/core/view.php';
require_once 'app/core/model.php';

class Controller {

    public View $view;
    public Model $model;

    public function __construct(View $view, Model $model) {
        $this->view = $view;
        $this->model = $model;
    }

    final public function get(): void {
        $this->view->render();
    }

}
