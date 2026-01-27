<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class CallbackController extends Controller {

    public function __construct() {
        global $model;
        parent::__construct(new View('callback', 'Обратная связь'), $model);

        $this->view->scripts = array (
            new JSScript('/public/js/jquery/calendar.js'),
            new JSScript('/public/js/jquery/callback_validation.js'),
        );
    }

}
