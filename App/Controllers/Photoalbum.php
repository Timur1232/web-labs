<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\JsScript;
use App\Core\View\View;
use App\Models\Photoalbum\PhotoalbumModel;

final class Photoalbum {
    public static function index(Request $req): Component {
        $model = PhotoalbumModel::default();
        return View::template_with_layout('photoalbum', title: 'Фотофльбом',
                                          data: ['model' => $model],
                                          scripts: [JsScript::from('/public/js/photoalbum.js')]);
    }
}
