<?php

namespace App\Controllers;

require_once 'app/core/view.php';
require_once 'app/core/request.php';
require_once 'app/views/callback.php';
require_once 'app/models/callback_validator.php';

use App\Core\{Request};
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use App\Models\CallbackValidator;
use App\Views\CallbackView;

final class Callback {
    const TITLE = 'Обратная связь';
    const CALLBACK_FORM_TEMPLATE = 'callback_form';
    const CALLBACK_GOOD_TEMPLATE = 'callback_good';

    public static function index(Request $req): void {
        $view = CallbackView::default();
        echo $view->render_layout(template_page: self::CALLBACK_FORM_TEMPLATE, title: self::TITLE);
    }

    public static function check(Request $req): void {
        $model = CallbackValidator::from($req->form);
        if ($req->htmx && count($req->url->query) !== 0) {
            $query_f = $req->url->query['f'];
            if (!$model->validate_by_query($query_f)) {
                return;
            }
            $errors = $model->get_errors_by_query($query_f);
            foreach ($errors as $err) {
                echo CallbackView::error_tag($err);
            }
        } else if (count($req->url->query) === 0) {
            $model->validate_all($req->form);
            $view = CallbackView::default();
            if ($model->has_any_error()) {
                echo $view
                    ->data('model', $model)
                    ->render_hx($req, template_page: self::CALLBACK_FORM_TEMPLATE, title: self::TITLE);
            } else {
                // TODO: saving callback
                Log::warning('saving not implemented');
                echo $view->render_hx($req, template_page: self::CALLBACK_GOOD_TEMPLATE, title: self::TITLE);
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
