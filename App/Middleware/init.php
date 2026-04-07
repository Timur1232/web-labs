<?php namespace App\Middleware;

use App\Config;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Log;
use App\Core\Middleware;
use App\Core\Model\DBModel;
use App\Models\Statistics\Statistic;
use Closure;

final class AdminAuth implements Middleware {
    public function apply(Request $req, Closure $next): Closure {
        return function (Request $req) use($next) {
            if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
                $_SESSION['is_admin'] = false;
                return Response::redirect('/login_admin');
            }
            return $next($req);
        };
    }
}

final class Tracking implements Middleware {
    public function apply(Request $req, Closure $next): Closure {
        return function (Request $req) use($next) {
            $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
            $record = Statistic::current($_SERVER['REQUEST_URI']);
            $res = $model->insert($record);
            if (!$res->ok) {
                Log::error(__METHOD__.": Unable to insert statistics for :\n".print_r($record, true));
            }
            return $next($req);
        };
    }
}
