<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class StudyController extends Controller {

    private View $test_view;

    public function __construct() {
        global $model;
        parent::__construct(new View('study', 'Учеба'), $model);

        $this->test_view = new View('test', 'Тест');
        $this->test_view->scripts = array (
            new JSScript('/public/js/jquery/test_form_validation.js'),
        );
    }

    public function test(): void {
        $this->test_view->render();
    }
}

