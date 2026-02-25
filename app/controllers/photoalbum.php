<?php

namespace App\Controllers;

require_once 'app/core/request.php';
require_once 'app/core/view/view.php';
require_once 'app/models/photoalbum.php';

use App\Core\Request;
use App\Core\View\View;
use App\Models\PhotoalbumModel;

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
