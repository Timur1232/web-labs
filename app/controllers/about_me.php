<?php

namespace App\Controllers\AboutMe;
use App\Core\{Request, View};

function index(Request $req): void {
    $view = new View('about_me', 'Обо мне');
    $view->render_layout();
}
