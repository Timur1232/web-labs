<?php

namespace App\Controllers;

require_once 'app/core/request.php';
require_once 'app/core/view.php';
require_once 'app/core/data_validator.php';
require_once 'app/core/helpers.php';

use App\Core\{View, Request, DataValidator};

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
        $lim_errs = DataValidator::for($req->form['lim'])
            ->with_rules([
                'is_int'   => is_integer(...),
                'solution' => fn($d) => (int)$d == 5,
            ])->with_dependences([
                'solution' => ['is_int'],
            ])->collect_errors();

        $lim_iter = DataValidator::map_error_messeges($lim_errs, [
            'is_empty' => 'Ну и что я должен с этим делать',
            'is_int'   => 'Нужно целое число',
            'solution' => 'БесПРЕДЕЛ!',
        ]);

        $series_errs = DataValidator::for($req->form['series'])
            ->with_rules([
                'solution' => fn($d) => $d === 'answ2'
            ])->collect_errors();

        $series_iter = DataValidator::map_error_messeges($series_errs, [
            'is_empty' => 'Ну и что я должен с этим делать',
            'solution' => 'Ольшанская выехала за тобой'
        ]);

        $hard_errs = DataValidator::for($req->form['hard_one'])
            ->with_rules([
                'solution' => fn($d) => $d !== '4'
            ])->collect_errors();

        $hard_iter = DataValidator::map_error_messeges($hard_errs, [
            'is_empty' => 'Ну и что я должен с этим делать',
            'solution' => 'Стоит подумать еще раз'
        ]);

    }
}

function is_integer(mixed $data): bool {
    return filter_var(trim($data), FILTER_VALIDATE_INT) !== false;
}

function is_email(mixed $data): bool {
    return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
}

