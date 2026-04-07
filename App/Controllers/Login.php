<?php namespace App\Controllers;

use App\Config;
use App\Core\Context\HTTPMethod;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Model\DBModel;
use App\Models\User;
use App\Views\AdminView;
use App\Views\CommonView;
use App\Views\LoginView;

final class Login {
    public const LOGIN_TITLE = 'Вход';
    public const LOGIN_PAGE_NAME = 'login';
    public const REGISTER_TITLE = 'Регистрация';
    public const REGISTER_PAGE_NAME = 'register';

    public static function login_form(Request $req): Response {
        $comp = LoginView::login_form();
        $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME);
        return Response::view($comp);
    }

    public static function register_form(Request $req): Response {
        $comp = LoginView::register_form();
        $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME);
        return Response::view($comp);
    }

    public static function register_post(Request $req): Response {
        if ($_SESSION['is_admin']) {
            return Response::redirect('/admin');
        }
        if (isset($_SESSION['login'])) {
            return Response::redirect('/');
        }

        $fio      = $req->form['fio'];
        $email    = $req->form['email'];
        $login    = $req->form['login'];
        $password = $req->form['password'];
        $password_again = $req->form['password_again'];

        if (!isset($fio) ||
            !isset($email) ||
            !isset($login) ||
            !isset($password)) {
            $msg = 'Нужно заполнить все поля.';
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME);
            return Response::view($comp);
        }

        if ($password !== $password_again) {
            $msg = 'Пароли не совпадают.';
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME);
            return Response::view($comp);
        }

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_by_id(User::class, $login);
        if ($res->ok) {
            $msg = 'Пользователь уже существует.';
            $comp = LoginView::register_form($msg);
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME);
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
            $comp = CommonView::layout($comp, self::REGISTER_TITLE, self::REGISTER_PAGE_NAME);
            return Response::view($comp);
        }

        $_SESSION['login'] = $login;

        return Response::redirect('/');
    }

    public static function login_post(Request $req): Response {
        if ($_SESSION['is_admin']) {
            return Response::redirect('/admin');
        }
        if (isset($_SESSION['login'])) {
            return Response::redirect('/');
        }

        $login    = $req->form['login'];
        $password = $req->form['password'];

        if (!isset($login) || !isset($password)) {
            $msg = 'Нужно заполнить все поля.';
            $comp = LoginView::login_form($msg);
            $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME);
            return Response::view($comp);
        }

        $password_hash = md5($password);

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_by_id(User::class, $login);
        if (!$res->ok) {
            $msg = 'Пользователя не существует.';
            $comp = LoginView::login_form($msg);
            $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME);
            return Response::view($comp);
        }
        $user = $res->val;

        if ($password_hash !== $user->password_hash) {
            $msg = 'Неправильный пароль.';
            $comp = LoginView::login_form($msg);
            $comp = CommonView::layout($comp, self::LOGIN_TITLE, self::LOGIN_PAGE_NAME);
            return Response::view($comp);
        }

        $_SESSION['login'] = $user->login;

        return Response::redirect('/');
    }

    public static function logout(Request $req): Response {
        $path = $req->url->query['path'] ?? '/';
        unset($_SESSION['login']);
        $_SESSION['is_admin'] = false;
        return Response::redirect($path);
    }

    public const ADMIN_LOGIN_TITLE = 'Вход';
    public const ADMIN_LOGIN_PAGE_NAME = 'login_admin';
    public static function login_admin(Request $req): Response {
        if ($req->method === HTTPMethod::GET) {
            return Response::view(CommonView::layout(AdminView::login_admin(), title: self::ADMIN_LOGIN_TITLE, page_name: self::ADMIN_LOGIN_PAGE_NAME));
        }

        if ($_SESSION['is_admin'] === true) {
            return Response::redirect('/admin');
        }

        $login = $req->form['login'];
        $password = $req->form['password'];
        if (!isset($login) || !isset($password)) {
            $msg = 'Логин и пароль необходимы.';
            $comp = AdminView::login_admin($msg);
            return Response::view(CommonView::layout($comp, title: self::ADMIN_LOGIN_TITLE, page_name: self::ADMIN_LOGIN_PAGE_NAME));
        }

        $hash = md5($password);
        if ($login === Config::ADMIN_LOGIN && $hash === Config::ADMIN_PASSWORD_HASH) {
            $_SESSION['is_admin'] = true;
            return Response::redirect('/admin');
        } else {
            $msg = 'неправильный логин или пароль.';
            $comp = AdminView::login_admin($msg);
            return Response::view(CommonView::layout($comp, title: self::ADMIN_LOGIN_TITLE, page_name: self::ADMIN_LOGIN_PAGE_NAME));
        }
    }
}
