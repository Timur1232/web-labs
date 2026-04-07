<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\View\Component;
use App\Core\View\JsScript;
use App\Models\Photoalbum\PhotoalbumModel;
use App\Views\CommonView;

final class Photoalbum {
    public static function index(Request $req): Response {
        $model = PhotoalbumModel::default();
        $comp = CommonView::template_with_layout('photoalbum', title: 'Фотофльбом',
                                          data: ['model' => $model],
                                          scripts: [JsScript::from('/public/js/photoalbum.js')]);
        return Response::view($comp);
    }
}
