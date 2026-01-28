<?php

require_once 'app/core/router.php';
require_once 'app/core/view.php';

require_once 'app/controllers/index.php';
require_once 'app/controllers/about_me.php';
require_once 'app/controllers/my_interests.php';
require_once 'app/controllers/study.php';
require_once 'app/controllers/photoalbum.php';
require_once 'app/controllers/callback.php';
require_once 'app/controllers/history.php';

use App\Core\Router;
use App\Controllers\{
    Index,
    AboutMe,
    Interests,
    Study,
    Photoalbum,
    Callback,
    History,
};

$router = new Router();

$router->GET('/',             Index\index(...));
$router->GET('/about_me',     AboutMe\index(...));
$router->GET('/my_interests', Interests\index(...));

$study = $router->group('/study');
$study->GET('/',              Study\index(...));
$study->GET('/test',          Study\test(...));

$router->GET('/photoalbum',   Photoalbum\index(...));
$router->GET('/callback',     Callback\index(...));
$router->GET('/history',      History\index(...));

$router->dispatch();













