<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\View\Js_Script;
use App\Models\Interests\Interests_Model;
use App\Views\Common_View;

final class Interests {
    public static function index(Request $req): Response {
        $model = Interests_Model::default();
        $comp = Common_View::template_with_layout('interests',
            title: 'Мои интересы',
            data: ['model' => $model],
            scripts: [Js_Script::from('/public/js/lists.js')],
            user: $req->additional['user'],
        );
        return Response::view($comp);
    }
}
