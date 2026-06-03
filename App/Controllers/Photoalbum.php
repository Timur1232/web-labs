<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\View\Js_Script;
use App\Models\Photoalbum_Model;
use App\Views\Common_View;

final class Photoalbum {
    public static function index(Request $req): Response {
        $model = Photoalbum_Model::default();
        $comp = Common_View::template_with_layout('photoalbum',
            title: 'Фотофльбом',
            data: ['model' => $model],
            scripts: [Js_Script::from('/public/js/photoalbum.js')],
            user: $req->additional['user'],
        );
        return Response::view($comp);
    }
}
