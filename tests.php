#!/bin/php
<?php

require_once './App/Core/Init.php';
spl_autoload_register(\App\Core\Init::autoload(...));

use App\Core\Test\TestDriver;

TestDriver::setup([
    App\Core\Model\FileCSVModel::class,
    App\Core\Route\URL::class,
]);
TestDriver::run_tests();
