<?php namespace App\Controllers;
use App\Core\Context\HTTP_Method;
use App\Core\Context\Response;
use App\Core\Context\Request;
use App\Core\Helpers\Error;
use App\Core\Helpers\Paginator;
use App\Core\Helpers\Log;
use App\Core\Helpers\My_Date_Time;
use App\Core\Model\Data_Validator;
use App\Core\Model\DB_Model;
use App\Core\Model\File_CSV_Model;
use App\Core\Model\AR_Reflect;
use App\Core\View\View;
use App\Views\Common_View;
use App\Views\Blog_View;
use App\Models\Dto\Comment_Record;
use App\Models\Dto\Blog_Record;

final class Blog {
    public static function index(Request $req): Response {
        $page = $req->binds['page'] ?? 0;
        $res = DB_Model::query(Blog_Record::select_all())
            ->fetch_all();
        if (!$res->ok) {
            $res->log();
            Error::assert(false, 'unable to find blogs');
        }
        $posts = AR_Reflect::construct_many(Blog_Record::class, $res->val);

        $p = Paginator::from($posts, per_page: 5);
        if ($page > $p->page_count()) {
            $page = 0;
        }
        $posts = $p->nth_page($page);
        $comp = View::template('blog_pages', data: ['page' => $page, 'posts' => $posts, 'page_count' => $p->page_count()]);
        $comp = Common_View::layout($comp, 'Блог', 'blog_page', user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function blog(Request $req): Response {
        $id = (int)$req->binds['id'] ?? null;
        $page = (int)$req->url->query['page'] ?? null;
        if (is_null($id) || is_null($page)) {
            return Response::redirect('/blog/all/0');
        }

        $res = DB_Model::query(Blog_Record::select_id())
            ->bind_values(['id' => $id])
            ->fetch();

        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $post = AR_Reflect::construct(Blog_Record::class, $res->val);

        $res = DB_Model::query(Comment_Record::select_blog_id())
            ->bind_values(['blog_id' => $id])
            ->fetch_all();

        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comments = AR_Reflect::construct_many(Comment_Record::class, $res->val);

        $user = $req->additional['user'];
        $comp = View::template('blog_page', data: ['post' => $post, 'page' => $page, 'user' => $user, 'comments' => $comments]);
        $comp = Common_View::layout($comp, 'Блог', 'blog_page', user: $user);
        return Response::view($comp);
    }

    public static function get_comments(Request $req): Response {
        // TODO: data validating
        $blog_id = $req->binds['id'];
        $res = DB_Model::query(Comment_Record::select_blog_id())
            ->bind_values(['blog_id' => $blog_id])
            ->fetch_all();
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comments = AR_Reflect::construct_many(Comment_Record::class, $res->val);
        return Response::view(Blog_View::comments_html($comments));
    }

    public static function comment_form(Request $req): Response {
        $blog_id = $req->binds['id'];
        $comp = Blog_View::comment_form($blog_id);
        if ($req->htmx) {
            return Response::view($comp);
        }
        return Response::view(View::empty(), code: 405);
    }

    public static function comment_button(Request $req): Response {
        $blog_id = $req->binds['id'];
        $comp = Blog_View::comment_button($blog_id);
        return Response::view($comp);
    }

    public static function post_comment(Request $req): Response {
        $blog_id = $req->binds['id'];
        $text = $req->form['text'];
        $user = $req->additional['user'];

        $comment = new Comment_Record(
            blog_id: $blog_id,
            user_name: $user->fio,
            text: $text,
        )->with_current_date();

        $res = DB_Model::query(Comment_Record::insert())
            ->bind_values($comment)
            ->execute();

        if (!$res->ok) {
            Error::internal_error();
        }

        return Response::redirect("/blog/{$blog_id}");
    }

    public const TITLE = 'Редактор блога';
    public const REDACTOR_PAGE_NAME = 'blog_redactor';

    public static function post(Request $req): Response {
        if ($req->method === HTTP_Method::GET) {
            $comp = Common_View::layout(
                View::template(self::REDACTOR_PAGE_NAME),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        };

        $image_file = $req->form_files['image'];
        [$ok, $errors] = self::validate_file($image_file);
        if (!$ok) {
            $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: [ 'msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        };

        $post = new Blog_Record(title: $req->form['title'], author: $req->form['author'], text: $req->form['text'])
            ->with_current_date();
        $new_image_path = '/public/media/blog/blog_image_'.$post->title.'-'.$post->datestr;
        rename($image_file['tmp_name'], '.'.$new_image_path);
        $post->image_path = $new_image_path;

        $res = DB_Model::query(Blog_Record::insert())
            ->bind_values($post)
            ->execute();
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
        if ($req->method === HTTP_Method::GET) {
            $comp = Common_View::layout(
                View::template(self::LOAD_BLOGS_PAGE_NAME),
                title: self::TITLE, page_name: self::LOAD_BLOGS_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        };
        $file = $req->form_files['posts'];
        [$ok, $errors] = self::validate_csv_file($file);
        if (!$ok) {
            $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: ['msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        };
        $res = File_CSV_Model::open($file['tmp_name'], sep: ',', expected_head: Blog_Record::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $csv = $res->val;

        $res = $csv->find_all(Blog_Record::class);
        if (!$res->ok) {
            $res->log();
            $msg = "Неправильный формат файла.";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(
                View::template(self::REDACTOR_PAGE_NAME, data: ['msg' => $msg]),
                title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
            return Response::view($comp);
        };
        $new_posts = $res->val;

        $res = DB_Model::query(Blog_Record::insert_many())
            ->bind_many_values($new_posts)
            ->execute();
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

        $res = DB_Model::query(Blog_Record::select_all())
            ->fetch_all();
        if (!$res->ok) {
            return Response::json(['error' => 'unable to find comments'], code: 404);
        }
        $comments = array_map(function($c) {
            return [
                'user_name' => $c['user_name'],
                'date' => My_Date_Time::to_date($c['datestr']),
                'text' => $c['text'],
            ];
        }, $res->val);
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

        $comment = new Comment_Record(
            blog_id: $blog_id,
            user_name: $user->fio,
            text: $text,
        )->with_current_date();
        $res = DB_Model::query(Comment_Record::insert())
            ->bind_values($comment)
            ->execute();
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

    public static function edit_form(Request $req): Response {
        $id = $req->binds['id'];

        $res = DB_Model::query(Blog_Record::select_id())
            ->bind_values(['id' => $id])
            ->fetch();
        if (!$res->ok) {
            return Response::redirect("/blogs");
        }
        $blog = AR_Reflect::construct(Blog_Record::class, $res->val);

        Log::trace(print_r($blog, true));

        $comp = View::template(self::REDACTOR_PAGE_NAME, data: ['blog' => $blog]);
        return Response::view($comp);
    }

    public static function edit_blog(Request $req): Response {
        $id = $req->binds['id'];

        $res = DB_Model::query(Blog_Record::select_id())
            ->bind_values(['id' => $id])
            ->fetch();
        if (!$res->ok) {
            return Response::redirect("/blogs");
        }
        $old_blog = AR_Reflect::construct(Blog_Record::class, $res->val);

        $post = new Blog_Record(id: $id, title: $req->form['title'], author: $req->form['author'], text: $req->form['text'])
            ->with_current_date();

        $has_new_image = isset($req->form_files['image']) && file_exists($req->form_files['image']['tmp_name']);
        if (isset($has_new_image) && $has_new_image) {
            if ($old_blog->image_path != null && file_exists($old_blog->image_path)) {
                unlink($old_blog->image_path);
            }
            $image_file = $req->form_files['image'];
            [$ok, $errors] = self::validate_file($image_file);
            if (!$ok) {
                $msg = "Неправильный формат файла:<br/><ul>{$errors}</ul><br/>";
                if ($req->htmx) return Response::view(View::msg_tag($msg));
                $comp = Common_View::layout(
                    View::template(self::REDACTOR_PAGE_NAME, data: [ 'msg' => $msg]),
                    title: self::TITLE, page_name: self::REDACTOR_PAGE_NAME, user: $req->additional['user']);
                return Response::view($comp);
            } else {
                $new_image_path = '/public/media/blog/blog_image_'.$post->title.'-'.$post->datestr;
                rename($image_file['tmp_name'], '.'.$new_image_path);
                $post->image_path = $new_image_path;
            }
        } else {
            $post->image_path = $old_blog->image_path;
        }

        $res = DB_Model::query(Blog_Record::update())
            ->bind_values($post)
            ->execute();
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }

        // TODO: add htmx support
        /* header('HX-Redirect: /blog/all/0'); */
        return Response::redirect('/blog/all/0');
    }

    /**
     * @param array<string,string> $file_info
     * @return array{bool, string[]}
     */
    private static function validate_file(array $file_info): array {
        $ext = [
            'png', 'jpg', 'jpeg', 'webp', 'gif',
        ];
        $errors = Data_Validator::for($file_info['name'])
            ->with_rules([
                'only_image' => fn($t) => in_array(array_last(explode('.', $t)), $ext),
            ])->collect_errors();
        return [count($errors) === 0, implode(";<br/>", Data_Validator::map_error_messeges($errors, [
            'only_image' => '<li>Файл должен быть картинкой;</li>',
        ]))];
    }

    /**
     * @param array<string,string> $file_info
     * @return array{bool, string[]}
     *
     * TODO: refactor this
     */
    private static function validate_csv_file(array $file_info): array {
        $errors = Data_Validator::for($file_info['name'])
            ->with_rules([
                'only_csv' => fn($t) => array_last(explode('.', $t)) === 'csv',
            ])->collect_errors();
        if ($file_info['error'] !== 0) {
            $errors[] = 'no_file_error';
        }
        return [count($errors) === 0, implode(";<br/>", Data_Validator::map_error_messeges($errors, [
            'is_empty' => '<li>Файл отсутствует;</li>',
            'only_csv' => '<li>Файл должен иметь расширение .csv;</li>',
        ]))];
    }

}
