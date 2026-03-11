<?php

spl_autoload_register(function ($class_name) {
    require_once str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
});

use App\Core\Helpers\Log;
use App\Core\Test\Test;
use App\Core\Test\TestDriver;

class Test1 {
    #[Test('test1')]
    public static function foo(): void {
        Log::printfln("Hello: foo");
    }
}

class Test2 {
    #[Test('test2')]
    public static function bar(): void {
        Log::printfln("Hello: bar");
        throw new Exception("Hi");
    }
    #[Test('test3')]
    public static function aa(): void {
        Log::printfln("Hello: aa");
    }
}

TestDriver::setup();
$t = new TestDriver([
    Test2::class,
    Test1::class,
]);

$t->run_tests();
