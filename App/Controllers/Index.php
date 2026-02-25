<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\View;

final class Index {
    public static function index(Request $req): void {
        $view = View::default();
        echo $view->render_layout(template_page: 'index', title: 'Мой сайт');
    }
}
