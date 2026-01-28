<?php

namespace App\Controllers;

require_once 'app/models/photoalbum.php';
require_once 'app/views/photoalbum.php';

use App\Core\Request;
use App\Models\PhotoalbumModel;
use App\Views\PhotoalbumView;

final class Photoalbum {
    public static function index(Request $req): void {
        $view = new PhotoalbumView();
        $model = new PhotoalbumModel();
        $view->data('model', $model);
        $view->script('/public/js/photoalbum.js');
        echo $view->render_layout(template_page: 'photoalbum', title: 'Фотофльбом');
    }
}
