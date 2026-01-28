<?php

namespace App\Controllers;
use App\Core\{Request, View};

final class Callback {
    public static function index(Request $req): void {
        $view = new View();
        $view->script('/public/js/calendar.js')
            ->script('/public/js/callback_validation.js');
        echo $view->render_layout(template_page: 'callback', title: 'Обратная связь');
    }
}
