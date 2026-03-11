<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\Helpers\{Error, Log};
use App\Core\View\Component;
use App\Core\View\View;
use App\Models\CallbackValidator;
use App\Views\CallbackView;
use App\Views\CommonView;

final class Callback {
    const TITLE = 'Обратная связь';
    const CALLBACK_FORM_TEMPLATE = 'callback_form';
    const CALLBACK_GOOD_TEMPLATE = 'callback_good';

    public static function index(Request $req): Component {
        return CommonView::template_with_layout(template_page: self::CALLBACK_FORM_TEMPLATE, title: self::TITLE);
    }

    public static function check(Request $req): Component {
        $model = CallbackValidator::from($req->form);
        if ($req->htmx && count($req->url->query) !== 0) {
            $query_f = $req->url->query['f'];
            if (!$model->validate_by_query($query_f)) {
                return View::empty();
            }
            $errors = $model->get_errors_by_query($query_f);
            return CallbackView::errors($errors);
        } else if (count($req->url->query) === 0) {
            $model->validate_all($req->form);
            if ($model->has_any_error()) {
                $comp = View::template(template_page: self::CALLBACK_FORM_TEMPLATE, data: ['model' => $model]);
                if ($req->htmx) return $comp;
                return CommonView::layout($comp, title: self::TITLE);
            } else {
                // TODO: saving callback
                Log::warning('saving not implemented');
                $comp = View::template(template_page: self::CALLBACK_GOOD_TEMPLATE);
                if ($req->htmx) return $comp;
                return CommonView::layout($comp, title: self::TITLE);
            }
        } else {
            ob_start();
            print_r($req);
            $req_str = ob_get_clean();
            Log::error("got invalid request: {$req_str}");
            Error::not_found($req->url->path);
        }
    }
}
