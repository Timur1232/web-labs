<?php
namespace App\Controllers;

use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Core\Model\FileCSVModel;
use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;
use App\Models\GuestBook\Messege;

final class GuestBook {
    public const TITLE = 'Гостевая книга';
    public const TEMPLATE_PAGE = 'guest_book_form';
    public const DB_NAME = 'messeges.inc';

    public static function index(Request $req): Component {
        $model = FileCSVModel::open_or_create(Messege::class, self::DB_NAME);
        if (!isset($model)) {
            Log::error('GuestBook: unable to open '.self::DB_NAME.' database');
            Error::internal_error();
        }
        return View::template_with_layout(self::TEMPLATE_PAGE, title: self::TITLE, data: [
            'model' => $model,
        ]);
    }

    public static function post_review(Request $req): Component {
        $review = new Messege(
            fio: $req->form['fio'],
            email: $req->form['email'],
            text: $req->form['text'],
        )->with_current_date();
        $model = FileCSVModel::open_or_create($review::class, self::DB_NAME);
        if (!isset($model)) {
            Log::error('GuestBook: unable to open '.self::DB_NAME.' database');
            Error::internal_error();
        }
        $model->insert($review);
        $comp = View::template(self::TEMPLATE_PAGE, data: [
            'model' => $model,
        ]);
        if ($req->htmx) return $comp;
        return View::layout($comp, title: self::TITLE);
    }
}
