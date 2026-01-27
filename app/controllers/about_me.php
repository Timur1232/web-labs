<?php

require_once 'app/core/controller.php';
require_once 'app/core/model.php';

class AboutMeController extends Controller {
    public function __construct() {
        global $model;
        parent::__construct(new View('about_me', 'Обо мне'), $model);
    }
}
