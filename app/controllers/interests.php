<?php

namespace App\Controllers;
require_once 'app/core/view.php';
require_once 'app/core/request.php';
require_once 'app/models/interests.php';

use App\Core\{View, Request};
use App\Models\InterestsModel;

final class Interests {
    public static function index(Request $req): void {
        $view = View::default();
        $model = InterestsModel::default();
        $view->data('model', $model);
        $view->script('/public/js/lists.js');
        echo $view->render_layout(template_page: 'interests', title: 'Мои интересы');
    }
}
