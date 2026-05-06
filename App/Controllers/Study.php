<?php namespace App\Controllers;
use App\Core\Helpers\Error;
use App\Core\Model\DBModel;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\View\View;
use App\Models\Test\TestModel;
use App\Models\Test\TestResult;
use App\Views\CommonView;
use App\Config;

final class Study {
    public const INDEX_PAGE_NAME = 'study';
    public const TEST_FORM_PAGE_NAME = 'test';
    public const RESULT_PAGE_NAME = 'test_result';
    public const SHOW_PAGE_NAME = 'show_test_page';

    public static function index(Request $req): Response {
        $comp = CommonView::template_with_layout(self::INDEX_PAGE_NAME, title: 'Учеба', user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function test(Request $req): Response {
        $comp = CommonView::template_with_layout(self::TEST_FORM_PAGE_NAME, title: 'Тест', user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function check_test(Request $req): Response {
        $model = TestModel::from($req->form);
        $model->check_test();
        $res = $model->save_results($req->form['fio']);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comp = View::template(self::RESULT_PAGE_NAME, data: ['model' => $model]);
        if ($req->htmx) return Response::view($comp);
        $comp = CommonView::layout($comp, title: 'Тест', page_name: self::RESULT_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function show_test_results(Request $req): Response {
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_all(TestResult::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comp = View::template(self::SHOW_PAGE_NAME, data: ['results' => $res->val]);
        if ($req->htmx) return Response::view($comp);
        $comp = CommonView::layout($comp, title: 'Результаты теста', page_name: self::SHOW_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }
}
