<?php

namespace App\Controllers;
require_once 'app/core/request.php';
require_once 'app/core/view.php';

use App\Core\{View, Request};

final class Study {
    public static function index(Request $req): void {
        $view = View::default();
        echo $view->render_layout(template_page: 'study', title: 'Учеба');
    }

    public static function test(Request $req): void {
        $test_view = View::default();
        $test_view->script('/public/js/test_form_validation.js');
        echo $test_view->render_layout(template_page: 'test', title: 'Тест');
    }

    public static function check_test(Request $req): void {
        
    }
}
