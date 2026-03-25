<?php
namespace App\Controllers;

use App\Core\Helpers\CSVFile;
use App\Core\Helpers\Error;
use App\Core\Helpers\Paginator;
use App\Core\Model\DataValidator;
use App\Core\Model\DBModel;
use App\Core\Model\FileCSVModel;
use App\Core\Route\HTTPMethod;
use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;
use App\Models\BlogRecord;
use App\Views\CommonView;
use App\Config;

final class Blog {
    public static function index(Request $req): Component {
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
        return CommonView::layout($comp, 'Блог', 'blog_page');
    }

    public static function blog(Request $req): Component {
        // TODO: redirecting to blogs if no id provided
        // make data validation
        $id = (int)$req->binds['id'];
        $page = (int)$req->url->query['page'];
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        $res = $model->find_by_id(BlogRecord::class, $id);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }

        $comp = View::template('blog_page', data: ['post' => $res->val, 'page' => $page]);
        return CommonView::layout($comp, 'Блог', 'blog_page');
    }

    public const TITLE = 'Редактор блога';
    public const REDACTOR_PAGE_NAME = 'blog_redactor';

    public static function post(Request $req): Component {
        if ($req->method === HTTPMethod::GET) {
            return CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME);
        }
        $image_file = $req->form_files['image'];
        [$ok, $errors] = self::validate_file($image_file);
        if (!$ok) {
            $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return View::msg_tag($msg);
            return CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: [ 'msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME);
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
        header('Location: /blog/all/0');
        return View::empty();
    }

    public const LOAD_BLOGS_PAGE_NAME = 'blog_load_csv';
    public static function load(Request $req): Component {
        if ($req->method === HTTPMethod::GET) {
            return CommonView::layout(
                View::template(self::LOAD_BLOGS_PAGE_NAME),
                title: self::TITLE, page_name: self::LOAD_BLOGS_PAGE_NAME);
        }
        $file = $req->form_files['posts'];
        [$ok, $errors] = self::validate_csv_file($file);
        if (!$ok) {
            $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return View::msg_tag($msg);
            return CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: ['msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME);
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
            if ($req->htmx) return View::msg_tag($msg);
            return CommonView::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: ['msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME);
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
        header('Location: /blog/all/0');
        return View::empty();
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
