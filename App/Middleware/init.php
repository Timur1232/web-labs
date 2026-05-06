<?php namespace App\Middleware;

use App\Config;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Log;
use App\Core\JwtToken;
use App\Core\Middleware;
use App\Core\Model\DBModel;
use App\Models\Statistics\Statistic;
use Closure;

final class AdminAuth implements Middleware {
    public function apply(Request $req, Closure $next): Closure {
        return function (Request $req) use($next) {
            $jwt = $_COOKIE['jwt_token'] ?? null;
            $user = JwtToken::get_user_from_jwt($jwt);
            if (is_null($user) || is_null($user->is_admin) || !$user->is_admin) {
                return Response::redirect('/login');
            }
            return $next($req);
        };
    }
}

final class UserAuth implements Middleware {
    public function apply(Request $req, Closure $next): Closure {
        return function (Request $req) use($next) {
            $jwt = $_COOKIE['jwt_token'] ?? null;
            $user = JwtToken::get_user_from_jwt($jwt);
            if (is_null($user)) {
                return Response::redirect('/login');
            }
            return $next($req);
        };
    }
}

final class GetUser implements Middleware {
    public function apply(Request $req, Closure $next): Closure {
        return function (Request $req) use($next) {
            $jwt = $_COOKIE['jwt_token'] ?? null;
            $user = JwtToken::get_user_from_jwt($jwt);
            if (!is_null($user)) {
                $req->additional['user'] = $user;
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
