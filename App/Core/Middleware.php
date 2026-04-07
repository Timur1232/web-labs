<?php namespace App\Core\Middleware;
use App\Core\Helpers\Error;
use App\Core\Route\Request;
use App\Core\View\Component;
use Closure;

interface Middleware {
    /**
     * @param Closure(Request): Component $next
     * @return Closure(Request): Component
     */
    function apply(Request $req, Closure $next): Closure;
}

final class AdminAuth implements Middleware {
    public function apply(Request $req, Closure $next): Closure {
        return function (Request $req) use($next) {
            if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
                $_SESSION['is_admin'] = false;
                Error::assert(false, 'AdminAuth: Add login controller');
            }
            return $next($req);
        };
    }
}
