<?php

namespace App\Controllers\Photoalbum;

require_once 'app/models/photoalbum.php';

use App\Core\{Request, View};
use App\Models\Photoalbum;

function index(Request $req): void {
    $view = new View('photoalbum', 'Фотоальбом');
    $view->data('photos', new Photoalbum());
    $view->script('/public/js/jquery/photoalbum.js');
    $view->render_layout();
}
