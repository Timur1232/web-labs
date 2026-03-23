#!/bin/php
<?php

spl_autoload_register(function ($class_name) {
    require_once str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
});

use App\Core\Test\TestDriver;

TestDriver::setup(array_slice($argv, 1));
TestDriver::run_tests();
