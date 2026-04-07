<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\View\Component;
use App\Core\View\JsScript;
use App\Models\Interests\InterestsModel;
use App\Views\CommonView;

final class Interests {
    public static function index(Request $req): Response {
        $model = InterestsModel::default();
        $comp = CommonView::template_with_layout('interests', title: 'Мои интересы',
                                          data: ['model' => $model],
                                          scripts: [JsScript::from('/public/js/lists.js')]);
        return Response::view($comp);
    }
}
