<?php namespace App\Controllers;
use App\Core\Helpers\Error;
use App\Core\Model\DB_Model;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Model\AR_Reflect;
use App\Core\View\View;
use App\Models\Test_Model;
use App\Models\Dto\Test_Result;
use App\Views\Common_View;

final class Study {
    public const INDEX_PAGE_NAME = 'study';
    public const TEST_FORM_PAGE_NAME = 'test';
    public const RESULT_PAGE_NAME = 'test_result';
    public const SHOW_PAGE_NAME = 'show_test_page';

    public static function index(Request $req): Response {
        $comp = Common_View::template_with_layout(self::INDEX_PAGE_NAME, title: 'Учеба', user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function test(Request $req): Response {
        $comp = Common_View::template_with_layout(self::TEST_FORM_PAGE_NAME, title: 'Тест', user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function check_test(Request $req): Response {
        $model = Test_Model::from($req->form);
        $model->check_test();
        $res = $model->save_results($req->form['fio']);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comp = View::template(self::RESULT_PAGE_NAME, data: ['model' => $model]);
        if ($req->htmx) return Response::view($comp);
        $comp = Common_View::layout($comp, title: 'Тест', page_name: self::RESULT_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function show_test_results(Request $req): Response {
        $res = DB_Model::query(Test_Result::select_all())
            ->fetch_all();
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $test_results = AR_Reflect::construct_many(Test_Result::class, $res->val);
        $comp = View::template(self::SHOW_PAGE_NAME, data: ['results' => $test_results]);
        if ($req->htmx) return Response::view($comp);
        $comp = Common_View::layout($comp, title: 'Результаты теста', page_name: self::SHOW_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }
}
