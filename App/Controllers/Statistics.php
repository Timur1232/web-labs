<?php namespace App\Controllers;

use App\Config;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Error;
use App\Core\Helpers\Paginator;
use App\Core\Model\DBModel;
use App\Core\View\View;
use App\Models\Statistics\Statistic;
use App\Views\CommonView;

final class Statistics {
    public static function index(Request $req): Response {
        $page = $req->binds['page'] ?? 0;
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        $res = $model->find_all(Statistic::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $stats = $res->val;

        usort($stats, function(Statistic $a, Statistic $b) {
            $da = $a->get_date();
            $db = $b->get_date();
            if ($da > $db) return -1;
            if ($da < $db) return 1;
            return 0;
        });

        $p = Paginator::from($stats, per_page: 5);
        if ($page > $p->page_count()) {
            $page = 0;
        }
        $stats = $p->nth_page($page);
        $comp = View::template('statistics_pages', data: ['page' => $page, 'stats' => $stats, 'page_count' => $p->page_count()]);
        $comp = CommonView::layout($comp, 'Статистика', 'statistics_pages');
        return Response::view($comp);
    }
}
