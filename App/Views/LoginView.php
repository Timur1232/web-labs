<?php namespace App\Views;

use App\Core\View\ComponentFunc;
use App\Core\View\View;

final class LoginView {
    public static function login_form(?string $msg = null): ComponentFunc {
        return View::func(function () use ($msg) {
            $msg ??= '';
            return <<<HTML
                <section id="login_form" class="content-container">
                    <form class="callback-form"
                        action="/login" method="post"
                    >
                        <label for="login">Логин:</label>
                        <input type="text" class="input-text" id="login" name="login"></input>
                        <br/>
                        <label for="password">Пароль:</label>
                        <input type="password" class="input-text" id="password" name="password"></input>
                        <br/>
                        <span class="error">{$msg}</span><br/>
                        <input class="button-submit" type="submit" value="Отправить" />
                        <input class="button-reset" type="reset" value="Очистить" />
                        <br/>
                        <a href="/register">Регистрация</a>
                    </form>
                </section>
            HTML;
        });
    }

    public static function register_form(?string $msg = null): ComponentFunc {
        return View::func(function () use ($msg) {
            $msg ??= '';
            return <<<HTML
                <section id="login_form" class="content-container">
                    <form class="callback-form"
                        action="/register" method="post"
                    >
                        <label for="fio">ФИО:</label>
                        <input type="text" class="input-text" id="fio" name="fio"></input>
                        <br/>
                        <label for="email">Email:</label>
                        <input type="email" class="input-text" id="email" name="email"></input>
                        <br/>
                        <label for="login">Логин:</label>
                        <input type="text" class="input-text" id="login" name="login" onblur="check_login_callback()"></input>
                        <br/>
                        <label for="password">Пароль:</label>
                        <input type="password" class="input-text" id="password" name="password"></input>
                        <br/>
                        <label for="password_again">Повторите пароль:</label>
                        <input type="password" class="input-text" id="password_again" name="password_again"></input>
                        <br/>
                        <span class="error">{$msg}</span><br/>
                        <input class="button-submit" type="submit" value="Отправить" />
                        <input class="button-reset" type="reset" value="Очистить" />
                        <br/>
                        <a href="/login">Логин</a>
                    </form>
                </section>
            HTML;
        });
    }
}
