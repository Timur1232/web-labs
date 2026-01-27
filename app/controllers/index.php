<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class IndexController extends Controller {
    public function __construct() {
        global $model;
        parent::__construct(new View('index', 'Мой сайт'), $model);
    }
}
