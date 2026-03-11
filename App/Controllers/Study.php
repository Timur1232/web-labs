<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;
use App\Models\Test\TestModel;
use App\Views\CommonView;

final class Study {
    public static function index(Request $req): Component {
        return CommonView::template_with_layout('study', title: 'Учеба');
    }

    public static function test(Request $req): Component {
        return CommonView::template_with_layout('test', title: 'Тест');
    }

    public static function check_test(Request $req): Component {
        $model = TestModel::from($req->form);
        $model->check_test();
        $comp = View::template('test_result', data: ['model' => $model]);
        if ($req->htmx) return $comp;
        return CommonView::layout($comp, title: 'Тест');
    }
}

