<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\Component;
use App\Views\CommonView;

final class AboutMe {
    public static function index(Request $req): Component {
        return CommonView::template_with_layout(template_page: 'about_me', title: 'Обо мне');
    }
}
