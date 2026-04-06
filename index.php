<?php

//////////////////////////////////////////////////////////////////////////
// ============================ Lab 1 Tour ============================ //
//////////////////////////////////////////////////////////////////////////
//                                                                      //
// 1. Start                     - index.php                             //
// 2. Router class              - app/core/router.php:15                //
// 3. RouteGroup class          - app/core/router.php:101               //
// 4. Request class             - app/core/request.php:51               //
// 5. URL class                 - app/core/request.php:5                //
// 6. View class                - app/core/view.php:31                  //
// 7. Layout function           - app/core/layout.php:25                //
// 8. Helper functions          - app/core/helpers.php:19               //
// 9. Controllers               - app/controllers/index.php:10          //
//                              - app/controllers/about_me.php:10       //
//                              - app/controllers/interests.php:12      //
//                              - app/controllers/study.php:14          //
//                              - app/controllers/photoalbum.php:13     //
//                              - app/controllers/callback.php:16       //
//                              - app/controllers/history.php:10        //
// 10. FormValidator class      - app/core/data_validator.php:242       //
// 11. DataValidator class      - app/core/data_validator.php:22        //
// 12. Photoalbum model         - app/models/photoalbum.php:18          //
// 13. Interests model          - app/models/interests.php:9            //
// 14. Interests model instance - app/models/instances/interests.php:17 //
// 15. Test results model       - app/models/test_result.php:9          //
// 16. Callback validator model - app/models/callback_validator.php:9   //
// 17. Photoalbum view          - app/views/photoalbum.php:11           //
// 18. Callback view            - app/views/callback.php:9              //
// 19. Templates                                                        //
//                                                                      //
//////////////////////////////////////////////////////////////////////////

require_once './App/Core/Init.php';
spl_autoload_register(\App\Core\Init::autoload(...));

if (!defined('STDIN')) define('STDIN', fopen('php://stdin', 'rb'));
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

use App\Controllers\Admin;
use App\Controllers\Blog;
use App\Core\Route\Router;
use App\Controllers\{
    Index, AboutMe, Interests, Study, Photoalbum, Callback, History, Raylib, GuestBook,
};

$router = Router::default();

// ====================[/]==================== //

$router->GET('/',           Index::index(...));
$router->GET('/about_me',   AboutMe::index(...));
$router->GET('/interests',  Interests::index(...));

$router->GET('/photoalbum', Photoalbum::index(...));
$router->GET('/callback',   Callback::index(...));
$router->GET('/history',    History::index(...));

$router->GET('/raylib',     Raylib::raylib(...));

// ====================[/blog]==================== //

$blog = $router->group('/blog');
$blog->GET('/all',       Blog::index(...));
$blog->GET('/all/:page', Blog::index(...));
$blog->GET('/:id',       Blog::blog(...));

// ====================[/study]==================== //

$study = $router->group('/study');
$study->GET('/', Study::index(...));

$test = $study->group('/test');
$test->GET('/',  Study::test(...));
$test->POST('/', Study::check_test(...));
$test->GET('/all_results', Study::show_test_results(...));

// ====================[/api]==================== //

$api = $router->group('/api');
$api->POST('/callback',     Callback::check(...));

// ====================[/guest_book]==================== //

$gb = $router->group('/guest_book');
$gb->GET('/',  GuestBook::index(...));
$gb->POST('/', GuestBook::post_review(...));

// ====================[/admin]==================== //

$admin = $router->group('/admin');
$admin->GET('/', Admin::index(...));

// ====================[/admin/blog]==================== //

$admin_blog = $admin->group('/blog');
$admin_blog->GET('/', Blog::post(...));
$admin_blog->POST('/post', Blog::post(...));
$admin_blog->GET('/load', Blog::load(...));
$admin_blog->POST('/load', Blog::load(...));

// ====================[/admin/guest_book]==================== //

$admin_gb = $admin->group('/guest_book');
$admin_gb->GET('/',          Admin::load_guest_book_index(...));
$admin_gb->POST('/append',   Admin::append_guest_book(...));
$admin_gb->POST('/override', Admin::override_guest_book(...));

$router->dispatch();
