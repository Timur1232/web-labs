<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\View;
use App\Models\Interests\InterestsModel;

final class Interests {
    public static function index(Request $req): void {
        $model = InterestsModel::default();
        $view = View::default()
            ->data('model', $model)
            ->script('/public/js/lists.js');
        echo $view->render_layout(template_page: 'interests', title: 'Мои интересы');
    }
}
