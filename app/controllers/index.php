<?php

namespace App\Controllers\Index;
use App\Core\{View, Request};

function index(Request $req): void {
    $view = new View('index', 'Мой сайт');
    $view->render_layout();
}
