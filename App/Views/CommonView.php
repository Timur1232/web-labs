<?php
namespace App\Views;

use App\Core\Helpers\Result;
use App\Core\View\Component;
use App\Core\View\View;

final class CommonView {
    /**
     * @param JsScript[] $scripts
     */
    public static function template_with_layout(
        string $template_page,
        string $title,
        array $data = [],
        ?string $page_name = null,
        array $scripts = []
    ): Component {
        $comp = View::template($template_page, data: $data);
        return self::layout($comp, title: $title, page_name: $page_name ?? $template_page, scripts: $scripts);
    }

    /**
     * @param JsScript[] $scripts
     */
    public static function layout(
        Component $comp,
        string $title,
        string $page_name,
        array $scripts = [],
    ): Component {
        return View::func(function () use ($comp, $title, $scripts, $page_name) {
            ?>
            <!DOCTYPE html>
            <html lang="ru-RU">
                <head>
                    <title><?= $title ?></title>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/public/styles/style.css">
                    <link rel="icon" href="/public/media/favicon.ico">
                    <script src="https://cdn.jsdelivr.net/npm/htmx.org@2.0.8/dist/htmx.min.js" integrity="sha384-/TgkGk7p307TH7EXJDuUlgG3Ce1UVolAOFopFekQkkXihi5u/6OCvVKyz1W+idaz" crossorigin="anonymous"></script>
                    <script type="text/javascript" src="/public/js/jquery/jquery.js"></script>
                    <script type="text/javascript" src="/public/js/menu_reveal_on_hover.js"></script>
                    <script type="text/javascript" src="/public/js/clock.js"></script>
                    <script type="text/javascript" src="/public/js/history.js"></script>
                    <script>
                        trackPage(document.title, getEndpoint());
                        htmx.on("htmx:beforeSwap", function(evt) {
                            evt.detail.shouldSwap = true;
                        });
                    </script>
                    <?php foreach ($scripts as $script): ?>
                        <?= $script->render_script() ?>
                    <?php endforeach ?>
                </head>

                <body class="flex-container">
                    <header>
                        <input type="checkbox" id="burger-menu-checkbox" class="burger-menu"
                            title="https://www.flaticon.com/ru/authors/andy-horvath" />
                        <label for="burger-menu-checkbox" class="burger-icon-closed">
                            <img src="/public/media/burger.png" alt="B" title="https://www.flaticon.com/ru/authors/andy-horvath" />
                        </label>
                        <label for="burger-menu-checkbox" class="burger-icon-opened">
                            <img src="/public/media/cross.png" alt="B" title="https://www.flaticon.com/ru/authors/andy-horvath" />
                        </label>
                        <nav>
                            <ul class="top-nav-bar">
                                <li>
                                    <a
                                        class="nav-link <?= $page_name == 'index' ? ' page-active' : '' ?>"
                                        href="/"
                                    >Главная</a>
                                </li>
                                <li>
                                    <a
                                        class="nav-link <?= $page_name == 'about_me' ? ' page-active' : '' ?>"
                                        href="/about_me"
                                    >Обо мне</a>
                                </li>
                                <li id="interests-link">
                                    <a
                                        class="nav-link <?= $page_name == 'interests' ? ' page-active' : '' ?>"
                                        href="/interests"
                                    >Мои интересы</a>
                                </li>
                                <li id="studies-link">
                                    <a
                                        class="nav-link <?= $page_name == 'study' || $page_name == 'test' ? ' page-active' : '' ?>"
                                        href="/study"
                                    >Учеба</a>
                                </li>
                                <li>
                                    <a
                                        class="nav-link <?= $page_name == 'photoalbum' ? ' page-active' : '' ?>"
                                        href="/photoalbum"
                                    >Фотоальбом</a>
                                </li>
                                <li id="callback-link">
                                    <a
                                        class="nav-link <?= $page_name == 'callback_form' ? ' page-active' : '' ?>"
                                        href="/callback"
                                    >Контакт</a>
                                </li>
                                <li>
                                    <a
                                        class="nav-link <?= $page_name == 'history' ? ' page-active' : '' ?>"
                                        href="/history"
                                    >История</a>
                                </li>
                                <!-- TODO: add check for admin session to show this tab -->
                                <li id="admin-link">
                                    <a
                                        class="nav-link <?= $page_name == 'admin' ? ' page-active' : '' ?>"
                                        href="/admin"
                                    >Админ</a>
                                </li>
                            </ul>
                        </nav>
                        <div id="clock" class="clock"></div>
                    </header>
                    <main>
                        <?php
                            $err = $comp->render();
                            if (!$err->ok) {
                                return $err;
                            }
                        ?>
                    </main>
                    <footer>
                        <section class="footer-content">
                            <h4>Контакты</h4>
                            <p>Email: <a href="mailto:timur.univercity@gmail.com">timur.univercity@gmail.com</a></p>
                        </section>
                    </footer>
                </body>
            </html>
            <?php
            return Result::OK();
        });
    }
}
