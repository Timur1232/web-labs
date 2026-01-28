<?php

namespace App\Controllers\Study;
use App\Core\{View, Request};

function index(Request $req): void {
    $view = new View('study', 'Учеба');
    $view->render_layout();
}

function test(Request $req): void {
    $test_view = new View('test', 'Тест');
    $test_view->script('/public/js/jquery/test_form_validation.js');
    $test_view->render_layout();
}
