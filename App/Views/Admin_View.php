<?php namespace App\Views;
use App\Core\View\Component_Func;
use App\Core\View\View;

final class Admin_View {

    public const TITLE = 'Im in da house';
    public const HOME_PAGE_NAME = 'home';
    public const LOAD_GB_PAGE_NAME = 'load_guest_book';

    public static function home(): Component_Func {
        return View::func(function () {
            return <<<HTML
            <section class="content-container">
                <ul>
                    <li><a href="/admin/guest_book">Загрузить гостевую книгу</a></li>
                    <li><a href="/admin/blog">Редактор Блога</a></li>
                </ul>
            </section>
            HTML;
        });
    }

    public static function guest_book_load_form(?string $msg = null): Component_Func {
        return View::func(function () use ($msg) {
            $msg = $msg ?? '';
            return <<<HTML
            <section id="admin_guest_book_form" class="content-container">
                <h2>Скачать файл с записями</h2>
                <a href="/public/messeges.inc" download>Скачать</a>

                <h2>Загрузить файл с записями с сервера</h2>
                <form action="/admin/guest_book/append" method="post"
                    enctype="multipart/form-data"
                    class="callback-form"
                    hx-post="/admin/guest_book/append"
                    hx-target="#msg"
                    hx-swap="outerHTML"
                >
                    <label for="messege">CSV файл сообщений</label><br/>
                    <input type="file" id="messege" name="messege" accept=".inc" required /><br/>
                    <input class="button-submit" type="submit" value="Добавить"
                        formaction="/admin/guest_book/append"
                    />
                    <input class="button-reset" type="submit" value="Перезаписать"
                        onclick="return confirm('Вы уверены? Это перезапишет существующие данные.')"
                        formaction="/admin/guest_book/override"
                    />
                </form>
                <span id="msg">{$msg}</span>
            </section>
            HTML;
        });
    }

    public static function login_admin(?string $msg = null): Component_Func {
        return View::func(function () use ($msg) {
            $msg = $msg ?? '';
            return <<<HTML
            <section id="login_admin" class="content-container">
                <form action="/login_admin" method="post"
                    class="callback-form"
                >
                    <h2>Вход админа</h2>

                    <label for="login">Логин</label><br/>
                    <input type="text" class="input-text" id="login" name="login" required /><br/>

                    <label for="password">Пароль</label><br/>
                    <input type="password" class="input-text" id="password" name="password" required /><br/>

                    <input class="button-submit" type="submit" value="Вход" />
                    <input class="button-reset" type="reset" value="Сброс" />
                </form>
                <span id="msg">{$msg}</span>
            </section>
            HTML;
        });
    }
}
