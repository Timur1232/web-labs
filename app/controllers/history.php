<?php

namespace App\Controllers\History;
use App\Core\{View, Request, JSScriptType};

function index(Request $req): void {
    $view = new View('history', 'История просмотра');
    $view->script('/public/js/jquery/set_history_reset.js', JSScriptType::Module);
    $view->render_layout();
}
