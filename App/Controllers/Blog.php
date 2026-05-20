<?php namespace App\Controllers;
use App\Core\Context\HTTPMethod as AppHTTPMethod;
use App\Core\Context\Response;
use App\Core\Helpers\Error;
use App\Core\Helpers\Paginator;
use App\Core\Model\DataValidator;
use App\Core\Model\DBModel;
use App\Core\Model\FileCSVModel;
use App\Core\Context\HTTPMethod;
use App\Core\Context\Request;
use App\Core\View\View;
use App\Models\BlogRecord;
use App\Views\CommonView;
use App\Config;
use App\Core\Helpers\Log;
use App\Core\View\ComponentFunc;
use App\Core\View\JsScript;
use App\Models\CommentRecord;
use App\Views\BlogView;

final class Blog {
    public static function index(Request $req): Response {
        $page = $req->binds['page'] ?? 0;
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        $res = $model->find_all(BlogRecord::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $posts = $res->val;

        usort($posts, function(BlogRecord $a, BlogRecord $b) {
            $da = $a->get_date();
            $db = $b->get_date();
            if ($da > $db) return -1;
            if ($da < $db) return 1;
            return 0;
        });

        $p = Paginator::from($posts, per_page: 5);
        if ($page > $p->page_count()) {
            $page = 0;
        }
        $posts = $p->nth_page($page);
        $comp = View::template('blog_pages', data: ['page' => $page, 'posts' => $posts, 'page_count' => $p->page_count()]);
        $comp = CommonView::layout($comp, 'Блог', 'blog_page', user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function blog(Request $req): Response {
        $id = (int)$req->binds['id'] ?? null;
        $page = (int)$req->url->query['page'] ?? null;
        if (is_null($id) || is_null($page)) {
            return Response::redirect('/blog/all/0');
        }
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        $res = $model->find_by_id(BlogRecord::class, $id);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }

        $post = $res->val;

        $res = $model->find_all(CommentRecord::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comments = array_filter($res->val, fn ($c) => $c->blog_id == $id);
        usort($comments, function(CommentRecord $a, CommentRecord $b) {
            $da = $a->get_date();
            $db = $b->get_date();
            if ($da > $db) return -1;
            if ($da < $db) return 1;
            return 0;
        });

        $user = $req->additional['user'];
        $comp = View::template('blog_page', data: ['post' => $post, 'page' => $page, 'user' => $user, 'comments' => $comments]);
        $comp = CommonView::layout($comp, 'Блог', 'blog_page', user: $user,
            scripts: [
                JsScript::from('/public/js/fetch_comments.js'),
            ],
        );
        return Response::view($comp);
    }

    public static function get_comments(Request $req): Response {
        $blog_id = $req->binds['id'];
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_all(CommentRecord::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comments = array_filter($res->val, fn ($c) => $c->blog_id == $blog_id);
        return Response::view(BlogView::comments_html($comments));
    }

    public static function comment_form(Request $req): Response {
        $blog_id = $req->binds['id'];
        $comp = BlogView::comment_form($blog_id);
        if ($req->htmx) {
            return Response::view($comp);
        }
        return Response::view(View::empty());
    }

    public static function comment_button(Request $req): Response {
        $blog_id = $req->binds['id'];
        $comp = BlogView::comment_button($blog_id);
        return Response::view($comp);
    }

    public static function post_comment(Request $req): Response {
        $blog_id = $req->binds['id'];
        $text = $req->form['text'];
        $user = $req->additional['user'];

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $comment = new CommentRecord(
            blog_id: $blog_id,
            user_name: $user->fio,
            text: $text,
        )->with_current_date();
        $res = $model->insert($comment);

        if (!$res->ok) {
            Error::internal_error();
        }

        return Response::redirect("/blog/{$blog_id}");
    }

    public const TITLE = 'Редактор блога';
    public const REDACTOR_PAGE_NAME = 'blog_redactor';

    public static function post(Request $req): Response {
        if ($req->method === AppHTTPMethod::GET) {
            $comp = CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        }
        $image_file = $req->form_files['image'];
        [$ok, $errors] = self::validate_file($image_file);
        if (!$ok) {
            $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: [ 'msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        }
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        $post = new BlogRecord(title: $req->form['title'], author: $req->form['author'], text: $req->form['text'])
            ->with_current_date();
        $new_image_path = '/public/media/blog/blog_image_'.$post->title.'-'.$post->datestr;
        rename($image_file['tmp_name'], '.'.$new_image_path);
        $post->image_path = $new_image_path;

        $res = $model->insert($post);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        // TODO: add htmx support
        /* header('HX-Redirect: /blog/all/0'); */
        return Response::redirect('/blog/all/0');
    }

    public const LOAD_BLOGS_PAGE_NAME = 'blog_load_csv';
    public static function load(Request $req): Response {
        if ($req->method === HTTPMethod::GET) {
            $comp = CommonView::layout(
                View::template(self::LOAD_BLOGS_PAGE_NAME),
                title: self::TITLE, page_name: self::LOAD_BLOGS_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        }
        $file = $req->form_files['posts'];
        [$ok, $errors] = self::validate_csv_file($file);
        if (!$ok) {
            $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: ['msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        }
        $res = FileCSVModel::open($file['tmp_name'], sep: ',', expected_head: BlogRecord::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $csv = $res->val;

        $res = $csv->find_all(BlogRecord::class);
        if (!$res->ok) {
            $res->log();
            $msg = "Неправильный формат файла.";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: ['msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        }
        $new_posts = $res->val;

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->insert($new_posts);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }

        // TODO: add htmx support
        /* header('HX-Redirect: /blog/all/0'); */
        return Response::redirect('/blog/all/0');
    }

    public static function get_all_comments(Request $req): Response {
        $blog_id = $req->binds['id'] ?? null;
        if (is_null($blog_id) || !is_numeric($blog_id)) {
            return Response::json(['error' => 'fuck you lether man'], code: 400);
        }
        $blog_id = (int)$blog_id;
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $res = $model->find_all(CommentRecord::class);
        if (!$res->ok) {
            return Response::json(['error' => 'unable to find comments'], code: 404);
        }
        $comments = $res->val;
        $comments = array_filter($res->val, fn ($c) => $c->blog_id == $blog_id);
        usort($comments, function(CommentRecord $a, CommentRecord $b) {
            $da = $a->get_date();
            $db = $b->get_date();
            if ($da > $db) return -1;
            if ($da < $db) return 1;
            return 0;
        });
        $comments = array_map(function($c) {
            return [
                'user_name' => $c->user_name,
                'date' => $c->format(),
                'text' => $c->text,
            ];
        }, $comments);
        return Response::json($comments);
    }

    public static function add_comment(Request $req): Response {
        $user = $req->additional['user'];
        $post_body = file_get_contents('php://input');
        Log::trace(print_r($post_body, true));
        $json = json_decode($post_body, true);
        Log::trace(print_r($json, true));

        $blog_id = $req->binds['id'] ?? null;
        $text = $json['text'] ?? null;
        $user = $req->additional['user'] ?? null;

        if (is_null($blog_id) || is_null($text) || is_null($user)) {
            return Response::json(['error' => 'invalid request'], 400);
        }

        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);
        $comment = new CommentRecord(
            blog_id: $blog_id,
            user_name: $user->fio,
            text: $text,
        )->with_current_date();
        $res = $model->insert($comment);
        if (!$res->ok) {
            $res->log(__METHOD__.': ');
            return Response::json(['error' => 'unable to insert comment'], 500);
        }
        return Response::json([
            'user_name' => $comment->user_name,
            'date' => $comment->format(),
            'text' => $comment->text,
        ]);
    }

    /**
     * @param array<string,string> $file_info
     * @return array[bool, string[]]
     */
    private static function validate_file(array $file_info): array {
        $ext = [
            'png', 'jpg', 'jpeg', 'webp', 'gif',
        ];
        $errors = DataValidator::for($file_info['name'])
            ->with_rules([
                'only_image' => fn($t) => in_array(array_last(explode('.', $t)), $ext),
            ])->collect_errors();
        if ($file_info['error'] !== 0) {
            $errors[] = 'no_file_error';
        }
        return [count($errors) === 0, implode(";<br/>", DataValidator::map_error_messeges($errors, [
            'is_empty' => '<li>Файл отсутствует;</li>',
            'only_image' => '<li>Файл должен быть картинкой;</li>',
        ]))];
    }

    /**
     * @param array<string,string> $file_info
     * @return array[bool, string[]]
     *
     * TODO: refactor this
     */
    private static function validate_csv_file(array $file_info): array {
        $errors = DataValidator::for($file_info['name'])
            ->with_rules([
                'only_csv' => fn($t) => array_last(explode('.', $t)) === 'csv',
            ])->collect_errors();
        if ($file_info['error'] !== 0) {
            $errors[] = 'no_file_error';
        }
        return [count($errors) === 0, implode(";<br/>", DataValidator::map_error_messeges($errors, [
            'is_empty' => '<li>Файл отсутствует;</li>',
            'only_csv' => '<li>Файл должен иметь расширение .csv;</li>',
        ]))];
    }

}
