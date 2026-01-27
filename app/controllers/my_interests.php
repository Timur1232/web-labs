<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class InterestsController extends Controller {
    public function __construct() {
        global $model;
        parent::__construct(new View('my_interests', 'Мои интересы'), $model);

        $this->view->scripts = array (
            new JSScript('/public/js/jquery/lists.js'),
        );
    }
}

