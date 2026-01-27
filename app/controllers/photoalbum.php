<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class PhotoalbumController extends Controller {
    public function __construct() {
        global $model;
        parent::__construct(new View('photoalbum', 'Фотоальбом'), $model);

        $this->view->scripts = array (
            new JSScript('/public/js/jquery/photoalbum.js'),
        );
    }
}

