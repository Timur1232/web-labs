<?php

namespace App\Controllers;
use App\Core\{View, Request};


final class Interests {
    public static function index(Request $req): void {
        $view = new View();
        $model = require 'app/models/instances/interests.php';
        $view->data('model', $model);
        $view->script('/public/js/lists.js');
        echo $view->render_layout(template_page: 'interests', title: 'Мои интересы');
    }
}
