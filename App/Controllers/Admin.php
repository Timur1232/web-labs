<?php namespace App\Controllers;
use App\Core\Context\Response;
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Core\Model\Data_Validator;
use App\Core\Model\File_CSV_Model;
use App\Core\Context\Request;
use App\Core\View\View;
use App\Models\Dto\Messege;
use App\Views\Admin_View;
use App\Views\Common_View;

final class Admin {
    public static function index(Request $req): Response {
        $comp = Admin_View::home();
        $comp = Common_View::layout($comp, title: Admin_View::TITLE, page_name: Admin_View::HOME_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function load_guest_book_index(Request $req): Response {
        $comp = Admin_View::guest_book_load_form();
        $comp = Common_View::layout($comp, title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function append_guest_book(Request $req): Response {
        $file = $req->form_files['messege'];
        [$ok, $errors] = self::validate_file($file);

        $user = $req->additional['user'];

        if (!$ok) {
            $msg = "Неправильный формат inc:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $res = File_CSV_Model::open($file['tmp_name']);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Неправильный формат csv.';
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }
        $model = $res->val;

        if (!$model->validate(Messege::class)) {
            $msg = 'Неправильный формат заголовка.';
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $res = $model->find_all(Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Ошибка чтения записей.';
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }
        $values = $res->val;

        $res = File_CSV_Model::open_or_create(Messege::DB_PATH, Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::assert(false, 'unable to open or create a file csv model');
        }
        $model = $res->val;

        $res = $model->insert($values);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::assert(false, 'unable to insert values in a file csv model');
        }

        $msg = 'Успешно!';
        if ($req->htmx) return Response::view(View::msg_tag($msg));
        $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
        return Response::view($comp);
    }

    public static function override_guest_book(Request $req): Response {
        $file = $req->form_files['messege'];
        [$ok, $errors] = self::validate_file($file);
        $user = $req->additional['user'];

        if (!$ok) {
            $msg = "Неправильный формат csv:<br/><ul>{$errors}</ul><br/>";
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $res = File_CSV_Model::open($file['tmp_name']);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Неправильный формат inc.';
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }
        $model = $res->val;

        if (!$model->validate(Messege::class)) {
            $msg = 'Неправильный формат заголовка.';
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }

        $res = $model->find_all(Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            $msg = 'Ошибка чтения записей.';
            if ($req->htmx) return Response::view(View::msg_tag($msg));
            $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
            return Response::view($comp);
        }
        $values = $res->val;

        // WARNING: Dangerous operation
        if (!unlink(Messege::DB_PATH)) {
            Log::error(__METHOD__.": Unable to delete file ".Messege::DB_PATH);
            Error::assert(false, 'unable to delete a file');
        }
        $res = File_CSV_Model::open_or_create(Messege::DB_PATH, Messege::class);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::assert(false, 'unable to open or create a file csv model');
        }
        $model = $res->val;

        $res = $model->insert($values);
        if (!$res->ok) {
            $res->log(__METHOD__);
            Error::assert(false, 'unable to insert values in file csv model');
        }

        $msg = 'Успешно!';
        if ($req->htmx) return Response::view(View::msg_tag($msg));
        $comp = Common_View::layout(Admin_View::guest_book_load_form($msg), title: Admin_View::TITLE, page_name: Admin_View::LOAD_GB_PAGE_NAME, user: $user);
        return Response::view($comp);
    }

    /**
     * @param array<string,string> $file_info
     * @return array{bool, string[]}
     */
    private static function validate_file(array $file_info): array {
        $errors = Data_Validator::for($file_info['name'])
            ->with_rules([
                'only_inc' => fn($t) => array_last(explode('.', $t)) === 'inc',
            ])->collect_errors();
        if ($file_info['error'] !== 0) {
            $errors[] = 'no_file_error';
        }
        return [count($errors) === 0, implode(";<br/>", Data_Validator::map_error_messeges($errors, [
            'is_empty' => '<li>Файл отсутствует;</li>',
            'only_inc' => '<li>Файл должен иметь расширение .inc;</li>',
        ]))];
    }
}
