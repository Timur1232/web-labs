<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\View;
use App\Models\Test\TestModel;

final class Study {
    public static function index(Request $req): void {
        $view = View::default();
        echo $view->render_layout(template_page: 'study', title: 'Учеба');
    }

    public static function test(Request $req): void {
        $test_view = View::default();
        echo $test_view->render_layout(template_page: 'test', title: 'Тест');
    }

    public static function check_test(Request $req): void {
        $model = TestModel::from($req->form);
        $model->check_test();
        $view = View::default()
            ->data('model', $model);
        echo $view->render_hx($req, template_page: 'test_result');
    }
}

