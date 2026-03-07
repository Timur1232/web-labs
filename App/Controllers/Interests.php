<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\JsScript;
use App\Core\View\View;
use App\Models\Interests\InterestsModel;

final class Interests {
    public static function index(Request $req): Component {
        $model = InterestsModel::default();
        return View::template_with_layout('interests', title: 'Мои интересы',
                                          data: ['model' => $model],
                                          scripts: [JsScript::from('/public/js/lists.js')]);
    }
}
