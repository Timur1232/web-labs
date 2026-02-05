<?php

namespace App\Controllers;

require_once 'app/core/view.php';
require_once 'app/core/request.php';
require_once 'app/views/callback.php';
require_once 'app/models/callback_validator.php';

use App\Core\{Request};
use App\Models\CallbackValidator;
use App\Views\CallbackView;

final class Callback {
    const TITLE = 'Обратная связь';
    const CALLBACK_FORM_TEMPLATE = 'callback_form';

    public static function index(Request $req): void {
        $view = CallbackView::default();
            // ->script('/public/js/callback_validation.js')
            // ->script('/public/js/calendar.js');
        echo $view->render_hx($req, template_page: self::CALLBACK_FORM_TEMPLATE, title: self::TITLE);
    }

    // TODO: Separate data validating and drawing.
    // Introduce some logic for correct data (ie saving)
    public static function check(Request $req): void {
        $model = CallbackValidator::default();
        if ($req->htmx && count($req->url->query) !== 0) {
            $errors = $model->validate_by_query($req);
            if ($errors === null) {
                return;
            }
            foreach ($errors as $err) {
                echo CallbackView::error_tag($err);
            }
        } else {
            $model->validate_all($req->form);
            $view = CallbackView::default()
                // ->script('/public/js/callback_validation.js')
                // ->script('/public/js/calendar.js')
                ->data('model', $model);
            echo $view->render_hx($req, template_page: self::CALLBACK_FORM_TEMPLATE, title: self::TITLE);
        }
    }
}
