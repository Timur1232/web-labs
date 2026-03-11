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
        Test::match_arrays([
            'a' => 'foo',
            'b' => 'bar',
            'c' => 'baz',
        ], [
            'a' => 'foo',
            'd' => 'bar',
            'c' => 'baz',
        ], 'AAAAAAAAAAAAAAAA');
    }
}

class Test2 {
    #[Test('test2')]
    public static function bar(): void {
        Log::printfln("Hello: bar");
        Test::crash("Hi");
    }
    #[Test('test3', stdout: 'probe/test_out_redir')]
    public static function aa(): void {
        Log::printfln("Hello: aa");
    }
    #[Test('test4')]
    public static function bb(): void {
        Test::match_files('test.csv', 'test1.csv', 'bad files');
        Test::match_files('test2.csv', 'test1.csv', 'bad files');
    }
}

TestDriver::setup([
    Test2::class,
    Test1::class,
]);
TestDriver::redirect([
    'stdout' => 'probe/test_out',
    'stderr' => 'probe/test_out',
]);
TestDriver::run_tests();
