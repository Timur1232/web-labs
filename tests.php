#!/bin/php
<?php

require_once './App/Core/Test/TestDriver.php';
use App\Core\Test\TestDriver;

TestDriver::setup(array_slice($argv, 1));
TestDriver::run_tests();
