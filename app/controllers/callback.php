<?php

namespace App\Controllers;

require_once 'app/core/view.php';
require_once 'app/core/request.php';

use App\Core\{Request, View};

final class Callback {
    public static function index(Request $req): void {
        $view = View::default();
        $view->script('/public/js/calendar.js')
            ->script('/public/js/callback_validation.js');
        echo $view->render_layout(template_page: 'callback', title: 'Обратная связь');
    }
}
