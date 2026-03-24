<?php
namespace App\Controllers;

use App\Core\Helpers\Error;
use App\Core\Helpers\Paginator;
use App\Core\Model\DBModel;
use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;
use App\Models\BlogRecord;
use App\Views\CommonView;
use Config;

final class Blog {
    public static function index(Request $req): Component {
        $page = $req->binds['page'] ?? 0;
        $model = DBModel::sqlite(Config::SQLITE_DB_PATH);

        // $posts = [
        //     new BlogRecord(author: 'Тимур', title: 'Тест1',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест2',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест3',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест4',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест5',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест6',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест7',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест8',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест9',  text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест10', text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест11', text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        //     new BlogRecord(author: 'Тимур', title: 'Тест12', text: 'Бла бла бла мой блог бла бла бла.', image_path: '/public/media/me.jpg')->with_current_date(),
        // ];
        // $i = 0;
        // foreach ($posts as $p) {
        //     $p->datestr[0] = strval($i%10);
        //     $i++;
        // }
        // $res = $model->insert($posts);
        // $res->log();
        // return View::empty();

        $res = $model->find_all(BlogRecord::class);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $posts = $res->val;

        usort($posts, function(BlogRecord $a, BlogRecord $b) {
            $da = $a->get_date();
            $db = $b->get_date();
            if ($da > $db) return 1;
            if ($da < $db) return -1;
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
}
