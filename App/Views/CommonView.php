<?php
namespace App\Views;

use App\Core\View\Component;
use App\Core\View\View;

final class CommonView {
    /**
     * @param JsScript[] $scripts
     * @param array<string,mixed> $data
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
            ob_start();
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
                    <!-- <script type="text/javascript" src="/public/js/history.js"></script> -->
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
                        <nav>
                            <ul class="top-nav-bar">
                                <li id="main-link">
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
                        <?php if ($_SESSION['is_admin']): ?>
                            <p style="position: fixed; top: calc(var(--header-height) / 2 - 17px);right: 100px; color: var(--main-light-color);padding: 8px 16px;">Вход за админа</p>
                            <form method="POST" action="/logout?path=<?= $_SERVER['REQUEST_URI'] ?>" >
                                <button id="logout"
                                    style="position: fixed; top: calc(var(--header-height) / 2 - 17px);right: 25px; color: var(--main-text-color);background-color: var(--main-light-color);padding: 8px 16px;"
                                >
                                    Выход
                                </button>
                            </form>
                        <?php endif ?>
                        <!-- <div id="clock" class="clock"></div> -->
                    </header>
                    <main>
                        <?= $comp->render() ?>
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
            return ob_get_clean();
        });
    }
}
