<?php namespace App\Controllers;
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Core\Model\File_CSV_Model;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Models\Dto\Messege;
use App\Views\Common_View;
use App\Views\Guest_Book_View;

final class Guest_Book {
    public const TITLE = 'Гостевая книга';
    public const TEMPLATE_PAGE = 'guest_book_form';

    public static function index(Request $req): Response {
        $user = $req->additional['user'];
        $res = File_CSV_Model::open_or_create(Messege::DB_PATH, Messege::class);
        if (!$res->ok) {
            Log::error('GuestBook: unable to open '.Messege::DB_PATH.' database');
            Error::internal_error();
        }
        $comp = Guest_Book_View::form(self::messeges_sorted($res->val));
        $comp = Common_View::layout($comp, title: self::TITLE, page_name: self::TEMPLATE_PAGE, user: $user);
        return Response::view($comp);
    }

    public static function post_review(Request $req): Response {
        $user = $req->additional['user'];
        $review = new Messege(
            fio: $req->form['fio'],
            email: $req->form['email'],
            text: $req->form['text'],
        )->with_current_date();
        $res = File_CSV_Model::open_or_create(Messege::DB_PATH, $review::class);
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
        $comp = Guest_Book_View::form(self::messeges_sorted($model));
        if ($req->htmx) return Response::view($comp);
        $comp = Common_View::layout($comp, title: self::TITLE, page_name: self::TEMPLATE_PAGE, user: $user);
        return Response::view($comp);
    }

    /**
     * @param File_CSV_Model<Messege> $model
     * @return Messege[]
     */
    private static function messeges_sorted(File_CSV_Model $model): array {
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
