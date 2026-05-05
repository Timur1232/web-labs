<?php

require __DIR__.'/vendor/autoload.php';
require_once './App/Core/Init.php';

spl_autoload_register(\App\Core\Init::autoload(...));

/* session_start(); */

if (!defined('STDIN')) define('STDIN', fopen('php://stdin', 'rb'));
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

use App\Controllers\Admin;
use App\Controllers\Blog;
use App\Controllers\Login;
use App\Controllers\Statistics;
use App\Middleware\AdminAuth;
use App\Core\Context\Router;
use App\Controllers\{
    Index, AboutMe, Interests, Study, Photoalbum, Callback, History, Raylib, GuestBook,
};
use App\Middleware\GetUser;
use App\Middleware\Tracking;
use App\Middleware\UserAuth;

$router = Router::default();

$common = $router->group('/', middleware: [
    Tracking::class,
    GetUser::class,
]);

// ====================[/]==================== //

$common->GET('/',             Index::index(...));
$common->GET('/about_me',     AboutMe::index(...));
$common->GET('/interests',    Interests::index(...));

$common->GET('/photoalbum',   Photoalbum::index(...));
$common->GET('/callback',     Callback::index(...));
$common->GET('/history',      History::index(...));

$common->GET('/raylib',       Raylib::raylib(...));

$common->POST('/logout',      Login::logout(...));

// ====================[/login]==================== //

$login = $common->group('/login');
$login->GET('/',  Login::login_form(...));
$login->POST('/', Login::login_post(...));

// ====================[/register]==================== //

$register = $common->group('/register');
$register->GET('/',  Login::register_form(...));
$register->POST('/', Login::register_post(...));

// ====================[/blog]==================== //

$blog = $common->group('/blog');
$blog->GET('/all',       Blog::index(...));
$blog->GET('/all/:page', Blog::index(...));
$blog->GET('/:id',       Blog::blog(...));

// ====================[/study]==================== //

$study = $common->group('/study');
$study->GET('/',           Study::index(...));

$test = $study->group('/test', middleware: [
    UserAuth::class,
]);
$test->GET('/',            Study::test(...));
$test->POST('/',           Study::check_test(...));
$test->GET('/all_results', Study::show_test_results(...));

// ====================[/api]==================== //

$api = $router->group('/api');
$api->POST('/callback', Callback::check(...));

// ====================[/guest_book]==================== //

$gb = $common->group('/guest_book');
$gb->GET('/',  GuestBook::index(...));
$gb->POST('/', GuestBook::post_review(...));

// ====================[/admin]==================== //

$admin = $router->group('/admin', middleware: [
    AdminAuth::class,
]);
$admin->GET('/', Admin::index(...));

// ====================[/admin/blog]==================== //

$admin_blog = $admin->group('/blog');
$admin_blog->GET('/',      Blog::post(...));
$admin_blog->POST('/post', Blog::post(...));
$admin_blog->GET('/load',  Blog::load(...));
$admin_blog->POST('/load', Blog::load(...));

// ====================[/admin/guest_book]==================== //

$admin_gb = $admin->group('/guest_book');
$admin_gb->GET('/',          Admin::load_guest_book_index(...));
$admin_gb->POST('/append',   Admin::append_guest_book(...));
$admin_gb->POST('/override', Admin::override_guest_book(...));

// ====================[/admin/stats]==================== //

$stats = $admin->group('/stats');
$stats->GET('/all',       Statistics::index(...));
$stats->GET('/all/:page', Statistics::index(...));

$router->dispatch();
