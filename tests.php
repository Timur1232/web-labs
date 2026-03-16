<?php

spl_autoload_register(function ($class_name) {
    require_once str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
});

use App\Core\Route\Request;
use App\Core\Route\URL;
use App\Core\Test\TestDriver;

TestDriver::setup([
    URL::class,
]);
TestDriver::run_tests();
