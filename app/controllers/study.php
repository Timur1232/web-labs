<?php

namespace App\Controllers;

require_once 'app/core/request.php';
require_once 'app/core/view.php';
require_once 'app/core/data_validator.php';
require_once 'app/core/helpers.php';
require_once 'app/models/test_result.php';

use App\Core\{View, Request};
use App\Models\TestModel;

final class Study {
    public static function index(Request $req): void {
        $view = View::default();
        echo $view->render_layout(template_page: 'study', title: 'Учеба');
    }

    public static function test(Request $req): void {
        $test_view = View::default();
            // ->script('/public/js/test_form_validation.js');
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

