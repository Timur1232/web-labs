<?php
namespace App\Core\Test;
use App\Core\Helpers\Log;
use Exception;
use ReflectionClass;

final class TestDriver {

    /**
     * @var string[] $test_classes
     */
    public static array $test_classes = [];
    /**
     * @var array<stirng,?string> $redirects
     */
    public static array $redirects = [
        'stdout' => null,
        'stdin' => null,
        'stderr' => null,
    ];
    /**
     * @var array<stirng,array> $opened_files
     */
    private static array $opened_files = [];

    /**
     * @param string[] $test_classes
     */
    public static function setup(array $test_classes = []): void {
        if (!defined('STDIN'))  define('STDIN',  fopen('php://stdin', 'rb'));
        if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
        if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));
        self::$test_classes = $test_classes;
    }

    /**
     * @param array<string,resource> $redirects
     */
    public static function redirect(array $redirects): void {
        if (isset($redirects['stdout'])) {
            Log::$stdout = $redirects['stdout'];
            self::$redirects['stdout'] = $redirects['stdout'];
        }
        if (isset($redirects['stderr'])) {
            Log::$stderr = $redirects['stderr'];
            self::$redirects['stderr'] = $redirects['stderr'];
        }
        if (isset($redirects['stdin'])) {
            Log::$stdin = $redirects['stdin'];
            self::$redirects['stdin'] = $redirects['stdin'];
        }
    }

    public static function print(string $msg = ''): void {
        fputs(STDOUT, "{$msg}");
    }

    public static function println(string $msg = ''): void {
        self::print($msg."\n");
    }

    public static function print_green(string $msg = ''): void {
        fputs(STDOUT, "\e[32m{$msg}\e[0m");
    }

    public static function print_err(string $msg = ''): void {
        fputs(STDERR, "\e[91m{$msg}\e[0m");
    }

    public static function println_green(string $msg = ''): void {
        self::print_green($msg."\n");
    }

    public static function println_err(string $msg = ''): void {
        self::print_err($msg."\n");
    }

    const STDOUT_REDIR_CUSTOM = 1<<0;
    const STDOUT_REDIR_GLOBAL = 1<<1;

    const STDERR_REDIR_CUSTOM = 1<<2;
    const STDERR_REDIR_GLOBAL = 1<<3;

    const STDIN_REDIR_CUSTOM = 1<<4;
    const STDIN_REDIR_GLOBAL = 1<<5;

    /**
     * @param ?array<string> $cases
     */
    public static function run_tests(?array $cases = null): void {
        self::println();
        $class_no = 0;
        $class_count = isset($cases) ? count($cases) : count(self::$test_classes);
        $fail_count = 0;
        $success_count = 0;
        foreach (self::$test_classes as $class_name) {
            if (!isset($cases) || in_array($class_name, $cases)) {
                $methods = self::get_test_methods($class_name);
                self::println_green("RUNING TESTS FOR: {$class_name}");
                $i = 1;
                foreach ($methods as [$m, $a]) {
                    self::println_green("\n> TEST {$i}: {$a->test_name} - {$m}:");
                    self::println('- - - - - - - - - - - - - - - ');

                    $redir_flags = self::apply_redirect($a);
                    try {
                        call_user_func($m);
                        $success_count++;
                        self::println_green("[TEST SUCCESSFUL]");
                    } catch (Exception $e) {
                        self::println_err("[TEST ERROR]");
                        self::println_err($e->getMessage());
                        $fail_count++;
                    }
                    self::println('- - - - - - - - - - - - - - - ');
                    self::restore_redirect($redir_flags);
                    self::close_custom_redirect_files($a);
                    $i++;
                }
                if ((++$class_no) < $class_count) self::println("\n==============================");
            }
        }
        self::close_redirect_files();

        self::println_green("Tests succeded: {$success_count}");
        if ($fail_count !== 0) self::println_err("Tests failed: {$fail_count}");
        else self::println_green("All tests succeded");
    }

    private static function apply_redirect(Test $a): int {
        $flags = 0;
        $stdout_path = null;
        if (isset($a->stdout)) {
            $stdout_path = $a->stdout;
            $flags |= self::STDOUT_REDIR_CUSTOM;
        } else if (isset(self::$redirects['stdout'])) {
            $stdout_path = self::$redirects['stdout'];
            $flags |= self::STDOUT_REDIR_GLOBAL;
        }
        if (isset($stdout_path)) {
            $stdout = match (isset(self::$opened_files[$stdout_path])) {
                true => self::$opened_files[$stdout_path][0],
                false => fopen($stdout_path, 'wb'),
            };
            if ($stdout === false) {
                self::println_err("Unable to open {$stdout_path} for STDOUT redirect.");
                $flags &= ~(self::STDOUT_REDIR_CUSTOM | self::STDOUT_REDIR_GLOBAL);
            } else {
                if (!isset(self::$opened_files[$stdout_path])) {
                    self::$opened_files[$stdout_path] = [$stdout, ($flags & self::STDOUT_REDIR_CUSTOM) !== 0];
                }
                Log::$stdout = $stdout;
                self::println("STDOUT redirected to {$stdout_path}.");
            }
        }

        $stderr_path = null;
        if (isset($a->stderr)) {
            $stderr_path = $a->stderr;
            $flags |= self::STDERR_REDIR_CUSTOM;
        } else if (isset(self::$redirects['stderr'])) {
            $stderr_path = self::$redirects['stderr'];
            $flags |= self::STDERR_REDIR_GLOBAL;
        }
        if (isset($stderr_path)) {
            $stderr = match (isset(self::$opened_files[$stderr_path])) {
                true => self::$opened_files[$stderr_path][0],
                false => fopen($stderr_path, 'wb'),
            };
            if ($stderr === false) {
                self::println_err("Unable to open {$stderr_path} for STDERR redirect.");
                $flags &= ~(self::STDERR_REDIR_CUSTOM | self::STDERR_REDIR_GLOBAL);
            } else {
                if (!isset(self::$opened_files[$stderr_path])) {
                    self::$opened_files[$stderr_path] = [$stderr, ($flags & self::STDERR_REDIR_CUSTOM) !== 0];
                }
                Log::$stderr = $stderr;
                self::println("STDERR redirected to {$stderr_path}.");
            }
        }

        $stdin_path = null;
        if (isset($a->stdin)) {
            $stdin_path = $a->stdin;
            $flags |= self::STDIN_REDIR_CUSTOM;
        } else if (isset(self::$redirects['stdin'])) {
            $stdin_path = self::$redirects['stdin'];
            $flags |= self::STDIN_REDIR_GLOBAL;
        }
        if (isset($stdin_path)) {
            $stdin = match (isset(self::$opened_files[$stdin_path])) {
                true => self::$opened_files[$stdin_path][0],
                false => fopen($stdin_path, 'rb'),
            };
            if ($stdin === false) {
                self::println_err("Unable to open {$stdin_path} for STDIN redirect.");
                $flags &= ~(self::STDIN_REDIR_CUSTOM | self::STDIN_REDIR_GLOBAL);
            } else {
                if (!isset(self::$opened_files[$stdin_path])) {
                    self::$opened_files[$stdin_path] = [$stdin, ($flags & self::STDIN_REDIR_CUSTOM) !== 0];
                }
                Log::$stdin = $stdin;
                self::println("STDIN redirected to {$stdin_path}.");
            }
        }

        return $flags;
    }

    private static function restore_redirect(int $flags): void {
        if ($flags & self::STDOUT_REDIR_CUSTOM !== 0 || $flags & self::STDOUT_REDIR_GLOBAL !== 0) {
            Log::$stdout = STDOUT;
        }
        if ($flags & self::STDERR_REDIR_CUSTOM !== 0 || $flags & self::STDERR_REDIR_GLOBAL !== 0) {
            Log::$stderr = STDERR;
        }
        if ($flags & self::STDIN_REDIR_CUSTOM !== 0 || $flags & self::STDIN_REDIR_GLOBAL !== 0) {
            Log::$stdin = STDIN;
        }
    }

    private static function close_custom_redirect_files(Test $a): void {
        foreach (self::$opened_files as $name => $pair) {
            [$file, $is_custom] = $pair;
            if ($is_custom && (
                $a->stdout === $name ||
                $a->stderr === $name ||
                $a->stdin  === $name
            )) {
                fclose($file);
                unset(self::$opened_files[$name]);
            }
        }
    }
    private static function close_redirect_files(): void {
        foreach (self::$opened_files as $pair) {
            [$file, $_] = $pair;
            fclose($file);
        }
    }

    /**
     * @template T
     * @param class-string<T> $class_name
     * @return array<int,array>
     */
    private static function get_test_methods(string $class_name): array {
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
