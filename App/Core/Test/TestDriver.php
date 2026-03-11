<?php
namespace App\Core\Test;
use Exception;
use ReflectionClass;

final class TestDriver {
    /**
     * @param class-string[] $test_classes
     */
    public function __construct(
        public array $test_classes,
    ) {}

    public static function setup(): void {
        define('STDIN', fopen('tests/stdin', 'rb'));
        define('STDOUT', fopen('tests/stdout', 'wb'));
        define('STDERR', fopen('tests/stderr', 'wb'));

        define('TEST_STDIN', fopen('php://stdin', 'rb'));
        define('TEST_STDOUT', fopen('php://stdout', 'wb'));
        define('TEST_STDERR', fopen('php://stderr', 'wb'));
    }

    public static function println_green(string $msg): void {
        fputs(TEST_STDOUT, "\e[32m{$msg}\e[0m\n");
    }

    public static function println_err(string $msg): void {
        fputs(TEST_STDERR, "\e[91m{$msg}\e[0m\n");
    }

    /**
     * @param ?array<string> $cases
     */
    public function run_tests(?array $cases = null): void {
        foreach ($this->test_classes as $class_name) {
            if (!isset($cases) || in_array($class_name, $cases)) {
                $methods = self::get_test_methods($class_name);
                self::println_green("RUN TESTS FOR: {$class_name}");
                $i = 1;
                foreach ($methods as [$m, $a]) {
                    self::println_green("> TEST {$i}: {$a->test_name} - {$m}");
                    self::println_green("[TEST OUTPUT]:\n");
                    try {
                        call_user_func($m);
                    } catch (Exception $e) {
                        self::println_err("[TEST ERROR]");
                        self::println_err("Exception: {$e->getMessage()}");
                    }
                    self::println_green("\n[TEST END]\n");
                }
            }
        }
    }

    /**
     * @template T
     * @param class-string<T> $class_name
     * @return array<int,array>
     */
    public static function get_test_methods(string $class_name): array {
        $r = new ReflectionClass($class_name);
        $ret = [];
        foreach ($r->getMethods() as $m) {
            foreach ($m->getAttributes() as $attr) {
                if ($attr->getName() === Test::class) {
                    $ret[] = ["{$class_name}::".$m->getName(), $attr->newInstance()];
                }
            }
        }
        return $ret;
    }
}
