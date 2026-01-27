<?php
function layout(App\Core\View $view): void {
?>
    <!DOCTYPE html>
    <html lang="ru-RU">
    <head>
    <title><?= $view->title ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/public/styles/style_scss.css">
        <link rel="icon" href="/public/media/favicon.ico">
        <script type="text/javascript" src="/public/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="/public/js/jquery/menu_reveal_on_hover.js"></script>
        <script type="text/javascript" src="/public/js/jquery/clock.js"></script>
        <script type="module" src="/public/js/jquery/history.js"></script>
        <script type="module" src="/public/js/jquery/track_page.js"></script>
        <?php
            if ($view->scripts != null) {
                foreach ($view->scripts as $script) {
                    $type = $script->type->value;
                    echo "<script type=\"$type\" src=\"$script->src\"></script>";
                }
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
                        <a class="nav-link <?= $view->page == 'index' ? ' page-active' : '' ?>" href="/">Главная</a>
                    </li>
                    <li>
                        <a class="nav-link <?= $view->page == 'about_me' ? ' page-active' : '' ?>" href="/about_me">Обо мне</a>
                    </li>
                    <li id="interests-link">
                        <a class="nav-link <?= $view->page == 'my_interests' ? ' page-active' : '' ?>" href="/my_interests">Мои интересы</a>
                    </li>
                    <li id="studies-link">
                        <a class="nav-link <?= $view->page == 'studies' ? ' page-active' : '' ?>" href="/study">Учеба</a>
                    </li>
                    <li>
                        <a class="nav-link <?= $view->page == 'photoalbum' ? ' page-active' : '' ?>" href="/photoalbum">Фотоальбом</a>
                    </li>
                    <li>
                        <a class="nav-link <?= $view->page == 'callback' ? ' page-active' : '' ?>" href="/callback">Контакт</a>
                    </li>
                    <li>
                        <a class="nav-link <?= $view->page == 'history' ? ' page-active' : '' ?>" href="/history">История</a>
                    </li>
                </ul>
            </nav>
            <div id="clock" class="clock"></div>
        </header>
        <main>
            <?php
                $page = 'app/views/'.$view->page.'.php';
                include $page;
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
