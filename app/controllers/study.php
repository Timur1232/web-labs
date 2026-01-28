<?php

namespace App\Controllers;
use App\Core\{View, Request};

final class Study {
    public static function index(Request $req): void {
        $view = new View();
        echo $view->render_layout(template_page: 'study', title: 'Учеба');
    }

    public static function test(Request $req): void {
        $test_view = new View();
        $test_view->script('/public/js/test_form_validation.js');
        echo $test_view->render_layout(template_page: 'test', title: 'Тест');
    }

    public static function check_test(Request $req): void {
        
    }
}
