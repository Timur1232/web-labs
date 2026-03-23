<?php
namespace App\Core\Test;
use App\Core\Helpers\Defer;
use App\Core\Helpers\Log;
use Exception;
use ParseError;
use ReflectionClass;
use TypeError;

final class TestDriver {

    /**
     * @var string[] $test_classes
     */
    public static array $test_classes = [];

    public static $stdin;
    public static $stdout;
    public static $stderr;

    /**
     * @param string[] $test_classes
     */
    public static function setup(array $test_classes = []): void {
        if (!defined('TEST_STDIN'))  define('TEST_STDIN',  fopen('php://stdin', 'rb'));
        if (!defined('TEST_STDOUT')) define('TEST_STDOUT', fopen('php://stdout', 'wb'));
        if (!defined('TEST_STDERR')) define('TEST_STDERR', fopen('php://stderr', 'wb'));
        self::$stdin  = TEST_STDIN;
        self::$stdout = TEST_STDOUT;
        self::$stderr = TEST_STDERR;
        self::$test_classes = $test_classes;
    }

    public static function print(string $msg = ''): void {
        fputs(TEST_STDOUT, "{$msg}");
    }

    public static function println(string $msg = ''): void {
        self::print($msg."\n");
    }

    public static function print_green(string $msg = ''): void {
        fputs(TEST_STDOUT, "\e[32m{$msg}\e[0m");
    }

    public static function print_err(string $msg = ''): void {
        fputs(TEST_STDOUT, "\e[91m{$msg}\e[0m");
    }

    public static function println_green(string $msg = ''): void {
        self::print_green($msg."\n");
    }

    public static function println_red(string $msg = ''): void {
        self::print_err($msg."\n");
    }

    public static function print_yellow(string $msg = ''): void {
        fputs(TEST_STDOUT, "\e[33m{$msg}\e[0m");
    }

    public static function println_yellow(string $msg = ''): void {
        self::print_yellow("{$msg}\n");
    }

    /**
     * @param ?array<string> $cases
     */
    public static function run_tests(?array $cases = null): void {
        self::println();
        $failed = [];
        $succeded = [];
        $skipped = [];

        $dev_null_handler = fopen('/dev/null', 'wb');
        Defer::d($_, fclose(...), $dev_null_handler);

        foreach (self::$test_classes as $class_name) {
            if (!isset($cases) || in_array($class_name, $cases)) {
                $methods = self::get_test_methods($class_name);
                if (count($methods) === 0) continue;
                self::println("RUNING TESTS FOR: {$class_name}");
                $i = 1;
                foreach ($methods as [$m, $a]) {
                    self::print("  {$i}) '{$a->test_name}' - {$class_name}::{$m->getName()}: ");

                    Log::$stdin = $dev_null_handler;
                    Log::$stdout = $dev_null_handler;
                    Log::$stderr = $dev_null_handler;

                    if (!$m->isStatic()) {
                        self::println_yellow("Non static methods not supported: {$class_name}::{$m->getName()}. Skipping.");
                        $skipped[] = ["{$class_name}::{$m->getName()}", $a];
                        continue;
                    }
                    if ($m->isPrivate() || $m->isProtected()) $m->setAccessible(true);

                    try {
                        $m->invoke(null);
                        if ($a->should_throw) {
                            self::println_red("SHOULD THROW: ERROR");
                            $failed[] = ["{$class_name}::{$m->getName()}", $a];
                        } else {
                            $succeded[] = ["{$class_name}::{$m->getName()}", $a];
                            self::println_green("OK");
                        }
                    } catch (Exception $e) {
                        if ($a->should_throw) {
                            self::println_green("SHOULD THROW: OK");
                            $succeded[] = ["{$class_name}::{$m->getName()}", $a];
                        } else {
                            self::println_red("ERROR");
                            self::println_red($e->getMessage());
                            $failed[] = ["{$class_name}::{$m->getName()}", $a];
                        }
                    } catch (TypeError $e) {
                        self::println_red("TYPE ERROR");
                        self::println_red($e->getMessage());
                        $failed[] = ["{$class_name}::{$m->getName()}", $a];
                    } catch (ParseError $e) {
                        self::println_red("PARSE ERROR");
                        self::println_red($e->getMessage());
                        $failed[] = ["{$class_name}::{$m->getName()}", $a];
                    }
                    $i++;
                }
            }
        }
        $success_count = count($succeded);
        self::println_green("\nTests succeded: {$success_count}");
        $skip_count = count($skipped);
        if ($skip_count !== 0) {
            self::println_yellow("Tests skipped: {$skip_count}");
            foreach ($skipped as $skip) {
                $throw = '';
                if ($skip[1]->should_throw) $throw = 'Should throw: ';
                self::println("  - {$throw}{$skip[0]}: '{$skip[1]->test_name}'");
            }
        }
        $fail_count = count($failed);
        if ($fail_count !== 0) {
            self::println_red("Tests failed: {$fail_count}");
            foreach ($failed as $fail) {
                $throw = '';
                if ($fail[1]->should_throw) $throw = 'Should throw: ';
                self::println("  - {$throw}{$fail[0]}: '{$fail[1]->test_name}'");
            }
        }
        else self::println_green("All tests succeded");
    }

    /**
     * @template T
     * @param class-string<T> $class_name
     * @return array<int,array>
     */
    private static function get_test_methods(string $class_name): array {
        try {
            $r = new ReflectionClass($class_name);
        } catch(Exception $e) {
            self::println_red("REFLECTION ERROR: {$class_name}");
            self::println_red($e->getMessage());
            return [];
        } catch (TypeError $e) {
            self::println_red("REFLECTION ERROR: {$class_name}");
            self::println_red($e->getMessage());
            return [];
        } catch (ParseError $e) {
            self::println_red("REFLECTION ERROR: {$class_name}");
            self::println_red($e->getMessage());
            return [];
        }
        $ret = [];
        foreach ($r->getMethods() as $m) {
            foreach ($m->getAttributes() as $attr) {
                if ($attr->getName() === Test::class) {
                    $ret[] = [$m, $attr->newInstance()];
                    break;
                }
            }
        }
        return $ret;
    }
}
