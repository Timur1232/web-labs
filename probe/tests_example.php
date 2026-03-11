<?php

spl_autoload_register(function ($class_name) {
    require_once str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
});

use App\Core\Helpers\Log;
use App\Core\Test\Test;
use App\Core\Test\TestDriver;

class Test1 {
    #[Test('array match')]
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
    #[Test('non static')]
    public function bebebe(): void {
        Log::printfln("if you see that - im broken");
    }
}

class Test2 {
    #[Test('private + crash')]
    private static function bar(): void {
        Log::printfln("Hello: bar");
        Test::crash("Hi");
    }
    #[Test('protected', stdout: 'probe/test_out_redir')]
    protected static function aa(): void {
        Log::printfln("Hello: aa");
    }
    #[Test('file match')]
    public static function bb(): void {
        Test::match_files('test.csv', 'test1.csv', 'bad files');
        Test::match_files('test2.csv', 'test1.csv', 'bad files');
    }
}

TestDriver::setup([
    Test1::class,
    Test2::class,
]);
TestDriver::redirect([
    'stdout' => 'probe/test_out',
    'stderr' => 'probe/test_out',
]);
TestDriver::run_tests();
