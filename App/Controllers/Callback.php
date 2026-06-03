<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\Helpers\Log;
use App\Core\View\View;
use App\Models\Callback_Validator;
use App\Views\Callback_View;
use App\Views\Common_View;

final class Callback {
    const TITLE = 'Обратная связь';
    const CALLBACK_FORM_TEMPLATE = 'callback_form';
    const CALLBACK_GOOD_TEMPLATE = 'callback_good';

    public static function index(Request $req): Response {
        $comp = Common_View::template_with_layout(template_page: self::CALLBACK_FORM_TEMPLATE, title: self::TITLE, user: $req->additional['user']);
        return Response::view($comp);
    }

    public static function check(Request $req): Response {
        $model = Callback_Validator::from($req->form);
        if ($req->htmx && count($req->url->query) !== 0) {
            $query_f = $req->url->query['f'];
            if (!$model->validate_by_query($query_f)) {
                return Response::view(View::empty());
            }
            $errors = $model->get_errors_by_query($query_f);
            $comp = Callback_View::errors($errors);
            return Response::view($comp);
        } else if (count($req->url->query) === 0) {
            $user = $req->additional['user'];
            $model->validate_all();
            if ($model->has_any_error()) {
                $comp = View::template(template_page: self::CALLBACK_FORM_TEMPLATE, data: ['model' => $model]);
                if ($req->htmx) return Response::view($comp);
                $comp = Common_View::layout($comp, title: self::TITLE, page_name: 'callback', user: $user);
                return Response::view($comp);
            } else {
                // TODO: saving callback
                Log::warning('saving not implemented');
                $comp = View::template(template_page: self::CALLBACK_GOOD_TEMPLATE);
                if ($req->htmx) return Response::view($comp);
                $comp = Common_View::layout($comp, title: self::TITLE, page_name: 'callback', user: $user);
                return Response::view($comp);
            }
        } else {
            ob_start();
            print_r($req);
            $req_str = ob_get_clean();
            Log::error("got invalid request: {$req_str}");
            return Response::empty(404);
        }
    }
}
