<?php

require_once 'app/core/core.php';
require_once 'app/controllers/controllers.php';

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

$router = Router::default();

$router->GET('/',             Index::index(...));
$router->GET('/about_me',     AboutMe::index(...));
$router->GET('/interests',    Interests::index(...));

$study = $router->group('/study');
$study->GET('/',              Study::index(...));
$study->GET('/test',          Study::test(...));
$study->POST('/test',         Study::check_test(...));

$router->GET('/photoalbum',   Photoalbum::index(...));
$router->GET('/callback',     Callback::index(...));
$router->GET('/history',      History::index(...));

$router->dispatch();













