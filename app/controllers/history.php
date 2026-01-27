<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class HistoryController extends Controller {
    public function __construct() {
        global $model;
        parent::__construct(new View('history', 'История просмотра'), $model);

        $this->view->scripts = array (
            new JSScript('/public/js/jquery/set_history_reset.js', JSScriptType::Module),
        );
    }
}
