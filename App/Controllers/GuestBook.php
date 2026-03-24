<?php
namespace App\Controllers;

use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Core\Model\FileCSVModel;
use App\Core\Route\Request;
use App\Core\View\Component;
use App\Models\GuestBook\Messege;
use App\Views\CommonView;
use App\Views\GuestBookView;

final class GuestBook {
    public const TITLE = 'Гостевая книга';
    public const TEMPLATE_PAGE = 'guest_book_form';

    public static function index(Request $req): Component {
        $res = FileCSVModel::open_or_create(Messege::DB_PATH, Messege::class);
        if (!$res->ok) {
            Log::error('GuestBook: unable to open '.Messege::DB_PATH.' database');
            Error::internal_error();
        }
        $comp = GuestBookView::form(self::messeges_sorted($res->val));
        return CommonView::layout($comp, title: self::TITLE, page_name: self::TEMPLATE_PAGE);
    }

    public static function post_review(Request $req): Component {
        $review = new Messege(
            fio: $req->form['fio'],
            email: $req->form['email'],
            text: $req->form['text'],
        )->with_current_date();
        $res = FileCSVModel::open_or_create(Messege::DB_PATH, $review::class);
        if (!$res->ok) {
            Log::error('GuestBook: unable to open '.Messege::DB_PATH.' database');
            Error::internal_error();
        }
        $model = $res->val;
        $res = $model->insert($review);
        if (!$res->ok) {
            $res->log();
            Error::internal_error();
        }
        $comp = GuestBookView::form(self::messeges_sorted($model));
        if ($req->htmx) return $comp;
        return CommonView::layout($comp, title: self::TITLE, page_name: self::TEMPLATE_PAGE);
    }

    /**
     * @param FileCSVModel<Messege> $model
     * @return Messege[]
     */
    private static function messeges_sorted(FileCSVModel $model): array {
        $res = $model->find_all(Messege::class);
        if (!$res->ok) {
            Error::internal_error();
        }
        $messeges = $res->val;
        usort($messeges, function(Messege $a, Messege $b) {
            $da = $a->get_date();
            $db = $b->get_date();
            if ($da > $db) return -1;
            if ($da < $db) return 1;
            return 0;
        });
        return $messeges;
    }
}
