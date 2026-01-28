<?php

namespace App\Controllers;
use App\Core\{Request, View};

final class AboutMe {
    public static function index(Request $req): void {
        $view = new View();
        echo $view->render_layout(template_page: 'about_me', title: 'Обо мне');
    }
}
