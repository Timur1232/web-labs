<?php

namespace App\Controllers;
use App\Core\{View, Request};

final class History {
    public static function index(Request $req): void {
        $view = new View();
        echo $view->render_layout(template_page: 'history', title: 'История просмотра');
    }
}
