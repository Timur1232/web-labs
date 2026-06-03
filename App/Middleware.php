<?php namespace App\Middleware;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Log;
use App\Jwt_Token;
use App\Core\Middleware;
use App\Core\Model\DB_Model;
use App\Models\Common_Sql\Common_Sql;
use App\Models\Dto\Statistic;
use Closure;

final class Admin_Auth implements Middleware {
    public function apply(Closure $next): Closure {
        return function (Request $req) use($next) {
            $jwt = $_COOKIE['jwt_token'] ?? null;
            $user = Jwt_Token::get_user_from_jwt($jwt);
            if (is_null($user) || is_null($user->is_admin) || !$user->is_admin) {
                return Response::redirect('/login');
            }
            return $next($req);
        };
    }
}

final class User_Auth implements Middleware {
    public function apply(Closure $next): Closure {
        return function (Request $req) use($next) {
            $jwt = $_COOKIE['jwt_token'] ?? null;
            $user = Jwt_Token::get_user_from_jwt($jwt);
            if (is_null($user)) {
                return Response::redirect('/login');
            }
            return $next($req);
        };
    }
}

final class Get_User implements Middleware {
    public function apply(Closure $next): Closure {
        return function (Request $req) use($next) {
            $jwt = $_COOKIE['jwt_token'] ?? null;
            $user = Jwt_Token::get_user_from_jwt($jwt);
            if (!is_null($user)) {
                $req->additional['user'] = $user;
            }
            return $next($req);
        };
    }
}

final class Tracking implements Middleware {
    public function apply(Closure $next): Closure {
        return function (Request $req) use($next) {
            $record = Statistic::current($_SERVER['REQUEST_URI']);
            $res = DB_Model::query(Statistic::insert())
                ->bind_values($record)
                ->execute();
            if (!$res->ok) {
                Log::error(__METHOD__.": Unable to insert statistics for :\n".print_r($record, true));
            }
            return $next($req);
        };
    }
}
