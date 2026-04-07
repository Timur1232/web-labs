<?php namespace App\Controllers;
use App\Config;
use App\Core\Context\HTTPMethod;
use App\Core\Context\Response;
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Core\Model\DataValidator;
use App\Core\Model\FileCSVModel;
use App\Core\Context\Request;
use App\Core\View\View;
use App\Models\GuestBook\Messege;
use App\Views\AdminView;
use App\Views\CommonView;

final class Admin {
    public static function index(Request $req): Response {
        $comp = AdminView::home();
        $comp = CommonView::layout($comp, title: AdminView::TITLE, page_name: AdminView::HOME_PAGE_NAME);
        return Response::view($comp);
    }

    public static function load_guest_book_index(Request $req): Response {
        $comp = AdminView::guest_book_load_form();
        $comp = CommonView::layout($comp, title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
        return Response::view($comp);
    }

    public static function append_guest_book(Request $req): Response {
        $file = $req->form_files['messege'];
        [$ok, $errors] = self::validate_file($file);

        if (!$ok) {
            $msg = "Неправильный формат inc:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }

        $res = FileCSVModel::open($file['tmp_name']);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Неправильный формат csv.';
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }
        $model = $res->val;

        if (!$model->validate(Messege::class)) {
            $msg = 'Неправильный формат заголовка.';
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }

        $res = $model->find_all(Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Ошибка чтения записей.';
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }
        $values = $res->val;

        $res = FileCSVModel::open_or_create(Messege::DB_PATH, Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::internal_error();
        }
        $model = $res->val;

        $res = $model->insert($values);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::internal_error();
        }

        $msg = 'Успешно!';
        if ($req->htmx) return View::msg_tag($msg);
        $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
        return Response::view($comp);
    }

    public static function override_guest_book(Request $req): Response {
        $file = $req->form_files['messege'];
        [$ok, $errors] = self::validate_file($file);

        $form = AdminView::guest_book_load_form();

        if (!$ok) {
            $msg = "Неправильный формат csv:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }

        $res = FileCSVModel::open($file['tmp_name']);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Неправильный формат inc.';
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }
        $model = $res->val;

        if (!$model->validate(Messege::class)) {
            $msg = 'Неправильный формат заголовка.';
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }

        $res = $model->find_all(Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Ошибка чтения записей.';
            if ($req->htmx) return View::msg_tag($msg);
            $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
            return Response::view($comp);
        }
        $values = $res->val;

        // WARNING: Dangerous operation
        if (!unlink(Messege::DB_PATH)) {
            Log::error(__METHOD__.": Unable to delete file ".Messege::DB_PATH);
            Error::internal_error();
        }
        $res = FileCSVModel::open_or_create(Messege::DB_PATH, Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::internal_error();
        }
        $model = $res->val;

        $res = $model->insert($values);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::internal_error();
        }

        $msg = 'Успешно!';
        if ($req->htmx) return View::msg_tag($msg);
        $comp = CommonView::layout(AdminView::guest_book_load_form($msg), title: AdminView::TITLE, page_name: AdminView::LOAD_GB_PAGE_NAME);
        return Response::view($comp);
    }

    /**
     * @param array<string,string> $file_info
     * @return array[bool, string[]]
     */
    private static function validate_file(array $file_info): array {
        $errors = DataValidator::for($file_info['name'])
            ->with_rules([
                'only_inc' => fn($t) => array_last(explode('.', $t)) === 'inc',
            ])->collect_errors();
        if ($file_info['error'] !== 0) {
            $errors[] = 'no_file_error';
        }
        return [count($errors) === 0, implode(";<br/>", DataValidator::map_error_messeges($errors, [
            'is_empty' => '<li>Файл отсутствует;</li>',
            'only_inc' => '<li>Файл должен иметь расширение .inc;</li>',
        ]))];
    }
}
