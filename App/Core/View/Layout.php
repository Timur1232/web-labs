<?php

namespace App\Core\View;

final class LayoutData {
    public function __construct(
        public string $title,
        public string $page,
        public string $template_page,
        public View $content,
    ) { }

    public static function new(
        string $title,
        string $page,
        string $template_page,
        View $content,
    ): self {
        return new self($title, $page, $template_page, $content);
    }
}

function layout(LayoutData $data): void { ?>
    <!DOCTYPE html>
    <html lang="ru-RU">
    <head>
        <title><?= $data->title ?></title>
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
        <?php
            foreach ($data->content->scripts as $script) {
                echo $script->render_script();
            }
        ?>
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
                            class="nav-link <?= $data->page == 'index' ? ' page-active' : '' ?>"
                            href="/"
                        >Главная</a>
                    </li>
                    <li>
                        <a
                            class="nav-link <?= $data->page == 'about_me' ? ' page-active' : '' ?>"
                            href="/about_me"
                        >Обо мне</a>
                    </li>
                    <li id="interests-link">
                        <a
                            class="nav-link <?= $data->page == 'interests' ? ' page-active' : '' ?>"
                            href="/interests"
                        >Мои интересы</a>
                    </li>
                    <li id="studies-link">
                        <a
                            class="nav-link <?= $data->page == 'study' || $data->page == 'test' ? ' page-active' : '' ?>"
                            href="/study"
                        >Учеба</a>
                    </li>
                    <li>
                        <a
                            class="nav-link <?= $data->page == 'photoalbum' ? ' page-active' : '' ?>"
                            href="/photoalbum"
                        >Фотоальбом</a>
                    </li>
                    <li>
                        <a
                            class="nav-link <?= $data->page == 'callback_form' ? ' page-active' : '' ?>"
                            href="/callback"
                        >Контакт</a>
                    </li>
                    <li>
                        <a
                            class="nav-link <?= $data->page == 'history' ? ' page-active' : '' ?>"
                            href="/history"
                        >История</a>
                    </li>
                </ul>
            </nav>
            <div id="clock" class="clock"></div>
        </header>
        <main>
            <?=
                $data->content->render($data->template_page);
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
}
