<?php

require_once 'app/core/router.php';

require_once 'app/controllers/index.php';
require_once 'app/controllers/about_me.php';
require_once 'app/controllers/my_interests.php';
require_once 'app/controllers/study.php';
require_once 'app/controllers/photoalbum.php';
require_once 'app/controllers/callback.php';
require_once 'app/controllers/history.php';

if (!isset($router)) {
    $router = new Router(array(
        'index'         => new IndexController(),
        'about_me'      => new AboutMeController(),
        'my_interests'  => new InterestsController(),
        'study'         => new StudyController(),
        'photoalbum'    => new PhotoalbumController(),
        'callback'      => new CallbackController(),
        'history'       => new HistoryController(),
    ));
}

$router->route();
