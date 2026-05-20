<?php namespace App\Controllers;
use App\Config;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Log;
use App\Core\JwtToken;
use App\Core\Model\DBModel;
use App\Core\View\JsScript;
use App\Core\View\View;
use App\Models\User;
use App\Views\CommonView;
use App\Views\LoginView;

final class Login {
    public const LOGIN_TITLE = 'Вход';
    public const LOGIN_PAGE_NAME = 'login';
    public const REGISTER_TITLE = 'Регистрация';
    public const REGISTER_PAGE_NAME = 'register';

    public static function login_form(Request $req): Response {
        $comp = LoginView::login_form();
        $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function register_form(Request $req): Response {
        $comp = LoginView::register_form();
        $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME,
            user: $req->additional['user'],
            scripts: [
                JsScript::from('/public/js/check_login.js', defer: true)
            ],
        );
        return Response::view($comp);
    }

    public static function check_login(Request $req): Response {
        $login = $req->form['login'] ?? null;
        if (is_null($login)) {
            return Response::text('false');
        }
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_by_id(User::class, $login);
        if ($res->ok) {
            return Response::text('false');
        } else {
            return Response::text('true');
        }
    }

    public static function register_post(Request $req): Response {
        $user = $req->additional['user'] ?? null;
        if (!is_null($user)) {
            if (!is_null($user->is_admin) && $user->is_admin) {
                return Response::redirect('/admin');
            }
            return Response::redirect('/');
        }

        $fio            = $req->form['fio'];
        $email          = $req->form['email'];
        $login          = $req->form['login'];
        $password       = $req->form['password'];
        $password_again = $req->form['password_again'];

        $user = $req->additional['user'];

        if (!isset($fio) ||
            !isset($email) ||
            !isset($login) ||
            !isset($password)) {
            $msg = 'Нужно заполнить все поля.';
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        if ($password !== $password_again) {
            $msg = 'Пароли не совпадают.';
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $msg = 'Пользователь уже существует.';
        if ($login === Config::ADMIN_LOGIN) {
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_by_id(User::class, $login);
        if ($res->ok) {
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $password_hash = md5($password);
        $user = new User(
            fio: $fio,
            email: $email,
            login: $login,
            password_hash: $password_hash,
        );

        $res = $model->insert($user);
        if (!$res->ok) {
            $msg = 'Не удалось создать пользователя.';
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $_SESSION['login'] = $login;

        return Response::redirect('/');
    }

    public static function login_post(Request $req): Response {
        $user = $req->additional['user'] ?? null;
        if (!is_null($user)) {
            if (!is_null($user->is_admin) && $user->is_admin) {
                return Response::redirect('/admin');
            }
            return Response::redirect('/');
        }

        $login    = $req->form['login'];
        $password = $req->form['password'];

        if (!isset($login) || !isset($password)) {
            $msg = 'Нужно заполнить все поля.';
            $comp = LoginView::login_form($msg);
            $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        $password_hash = md5($password);
        if ($login === Config::ADMIN_LOGIN && $password_hash === Config::ADMIN_PASSWORD_HASH) {
            $res = $model->find_by_id(User::class, $login);
            if (!$res->ok) {
                $msg = 'Произошла ошибка :/ Обратитесь куда-нибудь хз.';
                $comp = LoginView::login_form($msg);
                $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
                return Response::view($comp);
            }
            $user = $res->val;
            $jwt = JwtToken::generate_jwt($user);
            setcookie('jwt_token', $jwt, path: '/');
            return Response::redirect('/admin');
        }

        $res = $model->find_by_id(User::class, $login);
        if (!$res->ok) {
            $msg = 'Пользователя не существует.';
            $comp = LoginView::login_form($msg);
            $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
            return Response::view($comp);
        }
        $user = $res->val;

        if ($password_hash !== $user->password_hash) {
            $msg = 'Неправильный пароль.';
            $comp = LoginView::login_form($msg);
            $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $jwt = JwtToken::generate_jwt($user);
        setcookie('jwt_token', $jwt, path: '/');

        return Response::redirect('/');
    }

    public static function logout(Request $req): Response {
        $path = $req->url->query['path'] ?? '/';
        unset($_COOKIE['jwt_token']);
        setcookie('jwt_token', '');
        return Response::redirect($path);
    }

    public const ADMIN_LOGIN_TITLE = 'Вход';
    public const ADMIN_LOGIN_PAGE_NAME = 'login';
}
