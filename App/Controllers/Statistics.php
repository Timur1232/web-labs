<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Error;
use App\Core\Helpers\Paginator;
use App\Core\Model\AR_Reflect;
use App\Core\Model\DB_Model;
use App\Core\View\View;
use App\Models\Dto\Statistic;
use App\Views\Common_View;

final class Statistics {
    public static function index(Request $req): Response {
        $page = $req->binds['page'] ?? 0;

        $res = DB_Model::query(Statistic::select_all())
            ->fetch_all();
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $stats = AR_Reflect::construct_many(Statistic::class, $res->val);

        $p = Paginator::from($stats, per_page: 5);
        if ($page > $p->page_count()) {
            $page = 0;
        }
        $stats = $p->nth_page($page);
        $comp = View::template('statistics_pages', data: ['page' => $page, 'stats' => $stats, 'page_count' => $p->page_count()]);
        $comp = Common_View::layout($comp, 'Статистика', 'statistics_pages', user: $req->additional['user']);
        return Response::view($comp);
    }
}
