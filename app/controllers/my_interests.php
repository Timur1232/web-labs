<?php

namespace App\Controllers\Interests;
use App\Core\{View, Request};

function index(Request $req): void {
    $view = new View('my_interests', 'Мои интересы');
    $view->script('/public/js/jquery/lists.js');
    $view->render_layout();
}
