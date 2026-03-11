<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\Component;
use App\Views\CommonView;

final class History {
    public static function index(Request $req): Component {
        return CommonView::template_with_layout('history', title: 'История просмотра');
    }
}
