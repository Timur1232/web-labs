<?php

namespace App\Controllers;

require_once 'app/core/view.php';
require_once 'app/core/request.php';
require_once 'app/views/callback.php';

use App\Core\{Request};
use App\Views\CallbackView;

final class Callback {
    public static function index(Request $req): void {
        $view = CallbackView::default()
            ->script('/public/js/calendar.js')
            ->script('/public/js/callback_validation.js');
        echo $view->render_layout(template_page: 'callback', title: 'Обратная связь');
    }

    public static function check(Request $req): void {
        $view = CallbackView::default();
    }
}
