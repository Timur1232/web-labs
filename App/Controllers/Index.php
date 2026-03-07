<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;

final class Index {
    public static function index(Request $req): Component {
        return View::template_with_layout('index', title: 'Мой сайт');
    }
}
