<?php

require __DIR__.'/vendor/autoload.php';
require_once './App/Core/Init.php';

spl_autoload_register(\App\Core\Init::autoload(...));

if (!defined('STDIN')) define('STDIN', fopen('php://stdin', 'rb'));
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

use App\Config;
use App\Core\Context\Router;
use App\Controllers\{
    Index, About, Interests, Study, Photoalbum, Callback, History, Raylib,
    Guest_Book, Admin, Blog, Login, Statistics,
};
use App\Core\Model\DB_Model;
use App\Middleware\{
    Get_User, Tracking, User_Auth, Admin_Auth,
};

DB_Model::sqlite_connect(Config::SQLITE_DB_PATH);
Router::setup_current_request();
Router::$global_middleware = [
    Tracking::class,
    Get_User::class,
];

// ====================[/]==================== //

Router::GET('/',             Index::index(...));
Router::GET('/about_me',     About::index(...));
Router::GET('/interests',    Interests::index(...));

Router::GET('/photoalbum',   Photoalbum::index(...));
Router::GET('/callback',     Callback::index(...));
Router::GET('/history',      History::index(...));

Router::GET('/raylib',       Raylib::raylib(...));

Router::POST('/logout',      Login::logout(...));

// ====================[/login]==================== //

$login = Router::group('/login');
$login->GET('/',  Login::login_form(...));
$login->POST('/', Login::login_post(...));

// ====================[/register]==================== //

$register = Router::group('/register');
$register->GET('/',  Login::register_form(...));
$register->POST('/', Login::register_post(...));

// ====================[/blog]==================== //

$blog = Router::group('/blog');
$blog->GET('/all',       Blog::index(...));
$blog->GET('/all/:page', Blog::index(...));
$blog->GET('/:id',       Blog::blog(...));

// ====================[/study]==================== //

$study = Router::group('/study');
$study->GET('/',           Study::index(...));

$test = $study->group('/test', middleware: [
    User_Auth::class,
]);
$test->GET('/',            Study::test(...));
$test->POST('/',           Study::check_test(...));
$test->GET('/all_results', Study::show_test_results(...));

// ====================[/api]==================== //

$api = Router::group('/api');
$api->POST('/callback', Callback::check(...));
$api->POST('/check_login', Login::check_login(...));

$api->GET('/blog/:id/add_comment', Blog::comment_form(...));
/* $api->POST('/blog/:id/add_comment', Blog::post_comment(...), middleware: [ */
/*     GetUser::class, */
/* ]); */
$api->POST('/blog/:id/add_comment', Blog::add_comment(...));
$api->GET('/blog/:id/get_comments', Blog::get_comments(...));
$api->GET('/blog/:id/button',       Blog::comment_button(...));

// ====================[/guest_book]==================== //

$gb = Router::group('/guest_book');
$gb->GET('/',  Guest_Book::index(...));
$gb->POST('/', Guest_Book::post_review(...));

// ====================[/admin]==================== //

$admin = Router::group('/admin', middleware: [
    Admin_Auth::class,
]);
$admin->GET('/', Admin::index(...));

// ====================[/admin/blog]==================== //

$admin_blog = $admin->group('/blog');
$admin_blog->GET('/',         Blog::post(...));
$admin_blog->POST('/post',    Blog::post(...));
$admin_blog->GET('/load',     Blog::load(...));
$admin_blog->POST('/load',    Blog::load(...));
$admin_blog->GET('/:id/edit', Blog::edit_form(...));
$admin_blog->POST('/:id/edit', Blog::edit_blog(...));


// ====================[/admin/guest_book]==================== //

$admin_gb = $admin->group('/guest_book');
$admin_gb->GET('/',          Admin::load_guest_book_index(...));
$admin_gb->POST('/append',   Admin::append_guest_book(...));
$admin_gb->POST('/override', Admin::override_guest_book(...));

// ====================[/admin/stats]==================== //

$stats = $admin->group('/stats');
$stats->GET('/all',       Statistics::index(...));
$stats->GET('/all/:page', Statistics::index(...));
