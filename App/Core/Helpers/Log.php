<?php

namespace App\Core\Helpers;

final class Log {
    public static mixed $stdin = STDIN;
    public static mixed $stdout = STDOUT;
    public static mixed $stderr = STDERR;

    public static function printf(string $fmt, mixed ...$args): void {
        fprintf(self::$stdout, $fmt, ...$args);
    }

    public static function printfln(string $fmt, mixed ...$args): void {
        fprintf(self::$stdout, "$fmt\n", ...$args);
    }

    public static function println_green(string $msg): void {
        fputs(self::$stderr, "\e[32m{$msg}\e[0m\n");
    }

    public static function println_err(string $msg): void {
        fputs(self::$stderr, "\e[91m{$msg}\e[0m\n");
    }

    public static function trace(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(self::$stdout, "\e[37m[TRACE] {$fl}{$msg}\e[0m\n");
    }

    public static function info(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(self::$stdout, "\e[32m[INFO] {$fl}{$msg}\e[0m\n");
    }

    public static function warning(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(self::$stdout, "\e[33m[WARNING] {$fl}{$msg}\e[0m\n");
    }

    public static function error(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(self::$stderr, "\e[91m[ERROR] {$fl}{$msg}\e[0m\n");
    }

    public static function assert(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(self::$stderr, "\e[31m[ASSERT] {$fl}{$msg}\e[0m\n");
    }

    public static function log(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(self::$stdout, "[LOG] {$fl}{$msg}\n");
    }
}
