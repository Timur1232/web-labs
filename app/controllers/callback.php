<?php

namespace App\Controllers\Callback;
use App\Core\{Request, View};

function index(Request $req): void {
    $view = new View('callback', 'Обратная связь');
    $view->script('/public/js/jquery/calendar.js')
        ->script('/public/js/jquery/callback_validation.js');
    $view->render_layout();
}
