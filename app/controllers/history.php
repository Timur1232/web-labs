<?php

namespace App\Controllers;

require_once 'app/core/view/view.php';
require_once 'app/core/request.php';

use App\Core\Request;
use App\Core\View\View;

final class History {
    public static function index(Request $req): void {
        $view = View::default();
        echo $view->render_layout(template_page: 'history', title: 'История просмотра');
    }
}
