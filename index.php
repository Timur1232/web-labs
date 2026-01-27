<?php

require_once 'app/core/router.php';
require_once 'app/controllers/index.php';
require_once 'app/controllers/about_me.php';
require_once 'app/controllers/my_interests.php';
require_once 'app/controllers/study.php';
require_once 'app/controllers/photoalbum.php';
require_once 'app/controllers/callback.php';
require_once 'app/controllers/history.php';

use App\Core\Router;

$router = new Router();

$router->GET('/', [App\Controllers\IndexController::class, 'get']);
$router->GET('/about_me', [App\Controllers\AboutMeController::class, 'get']);
$router->GET('/my_interests', [App\Controllers\InterestsController::class, 'get']);
$router->GET('/study', [App\Controllers\StudyController::class, 'get']);
$router->GET('/study/test', [App\Controllers\StudyController::class, 'test']);
$router->GET('/photoalbum', [App\Controllers\PhotoalbumController::class, 'get']);
$router->GET('/callback', [App\Controllers\CallbackController::class, 'get']);
$router->GET('/history', [App\Controllers\HistoryController::class, 'get']);

$router->route($_SERVER['REQUEST_URI']);
