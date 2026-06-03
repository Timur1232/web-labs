<?php namespace App\Controllers;
use App\Config;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Model\AR_Reflect;
use App\Jwt_Token;
use App\Core\Model\DB_Model;
use App\Core\View\Js_Script;
use App\Models\Dto\User;
use App\Views\Common_View;
use App\Views\Login_View;

final class Login {
    public const LOGIN_TITLE = 'Вход';
    public const LOGIN_PAGE_NAME = 'login';
    public const REGISTER_TITLE = 'Регистрация';
    public const REGISTER_PAGE_NAME = 'register';

    public static function login_form(Request $req): Response {
        $comp = Login_View::login_form();
        $comp = Common_View::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function register_form(Request $req): Response {
        $comp = Login_View::register_form();
        $comp = Common_View::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME,
            user: $req->additional['user'],
            scripts: [
                Js_Script::from('/public/js/check_login.js', defer: true)
            ],
        );
        return Response::view($comp);
    }

    public static function check_login(Request $req): Response {
        $login = $req->form['login'] ?? null;
        if (is_null($login)) {
            return Response::text('false');
        }
        $res = DB_Model::query(User::select_login())
            ->bind_values(['login' => $login])
            ->fetch();
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
            $comp = Login_View::register_form($msg);
            $comp = Common_View::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        if ($password !== $password_again) {
            $msg = 'Пароли не совпадают.';
            $comp = Login_View::register_form($msg);
            $comp = Common_View::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $msg = 'Пользователь уже существует.';
        if ($login === Config::ADMIN_LOGIN) {
            $comp = Login_View::register_form($msg);
            $comp = Common_View::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $res = DB_Model::query(User::select_login())
            ->bind_values(['login' => $login])
            ->fetch();
        if ($res->ok) {
            $comp = Login_View::register_form($msg);
            $comp = Common_View::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $password_hash = md5($password);
        $user = new User(
            fio: $fio,
            email: $email,
            login: $login,
            password_hash: $password_hash,
        );

        $res = DB_Model::query(User::insert())
            ->bind_values($user)
            ->execute();
        if (!$res->ok) {
            $msg = 'Не удалось создать пользователя.';
            $comp = Login_View::register_form($msg);
            $comp = Common_View::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME, user: $user);
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
            $comp = Login_View::login_form($msg);
            $comp = Common_View::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
            return Response::view($comp);
        }


        $password_hash = md5($password);
        if ($login === Config::ADMIN_LOGIN && $password_hash === Config::ADMIN_PASSWORD_HASH) {
            $res = DB_Model::query(User::select_login())
                ->bind_values(['login' => $login])
                ->fetch();
            if (!$res->ok) {
                $msg = 'Произошла ошибка :/ Обратитесь куда-нибудь хз.';
                $comp = Login_View::login_form($msg);
                $comp = Common_View::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
                return Response::view($comp);
            }
            $user = AR_Reflect::construct(User::class, $res->val);
            $jwt = Jwt_Token::generate_jwt($user);
            setcookie('jwt_token', $jwt, path: '/');
            return Response::redirect('/admin');
        }

        $res = DB_Model::query(User::select_login())
            ->bind_values(['login' => $login])
            ->fetch();
        if (!$res->ok) {
            $msg = 'Пользователя не существует.';
            $comp = Login_View::login_form($msg);
            $comp = Common_View::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
            return Response::view($comp);
        }
        $user = AR_Reflect::construct(User::class, $res->val);

        if ($password_hash !== $user->password_hash) {
            $msg = 'Неправильный пароль.';
            $comp = Login_View::login_form($msg);
            $comp = Common_View::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $jwt = Jwt_Token::generate_jwt($user);
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
