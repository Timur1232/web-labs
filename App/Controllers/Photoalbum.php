<?php

namespace App\Controllers;

use App\Core\Route\Request;
use App\Core\View\View;
use App\Models\Photoalbum\PhotoalbumModel;

final class Photoalbum {
    public static function index(Request $req): void {
        $model = PhotoalbumModel::default();
        $view = View::default();
        $view
            ->data('model', $model)
            ->script('/public/js/photoalbum.js');
        echo $view->render_layout(template_page: 'photoalbum', title: 'Фотофльбом');
    }
}
