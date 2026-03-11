<?php
namespace App\Core\Test;
use Attribute;
use Exception;

#[Attribute(Attribute::TARGET_METHOD)]
final class Test {
    public function __construct(
        public string $test_name = 'no name',
        public ?string $stdout = null,
        public ?string $stdin = null,
        public ?string $stderr = null,
    ) {}

    public static function assert(bool $cond, ?string $msg = null): void {
        if (!$cond) {
            $msg = isset($msg) ? "\nMessage: {$msg}" : '';
            throw new Exception("assert: \$cond == false.{$msg}");
        }
    }

    public static function crash(?string $msg = null): void {
        $msg = isset($msg) ? "\nMessage: {$msg}" : '';
        throw new Exception("crash: Programm killed.{$msg}");
    }

    /**
     * @param array<mixed,mixed> $arr1
     * @param array<mixed,mixed> $arr2
     */
    public static function match_arrays(array $arr1, array $arr2, ?string $msg = null): void {
        $msg = isset($msg) ? "\nMessage: {$msg}" : '';
        $arr1_len = count($arr1);
        $arr2_len = count($arr2);
        if ($arr1_len !== $arr2_len) {
            throw new Exception("match_arrays: Sizes of arrays dont match: count(\$arr1) == {$arr1_len}, count(\$arr2) == {$arr2_len}.{$msg}");
        }

        $diff = [];
        foreach ($arr1 as $k1 => $v1) {
            if (!isset($arr2[$k1]) || $arr2[$k1] !== $v1) {
                $diff[$k1] = $v1;
            }
        }

        if (count($diff) !== 0) {
            $diff_str = print_r($diff, true);
            $arr1_str = print_r($arr1, true);
            $arr2_str = print_r($arr2, true);
            throw new Exception(<<<STR
                match_arrays: Arrays keys and values dont match.{$msg}
                Array1: {$arr1_str}
                Array2: {$arr2_str}
                Diff (array 1): {$diff_str}
                STR);
        }
    }

    /**
     * @param array<mixed,mixed> $arr1
     * @param array<mixed,mixed> $arr2
     */
    public static function match_arrays_values(array $arr1, array $arr2, ?string $msg = null): void {
        $msg = isset($msg) ? "\nMessage: {$msg}" : '';
        $arr1_len = count($arr1);
        $arr2_len = count($arr2);
        if ($arr1_len !== $arr2_len) {
            throw new Exception("match_arrays_values: Sizes of arrays dont match: count(\$arr1) == {$arr1_len}, count(\$arr2) == {$arr2_len}.{$msg}");
        }
        $diff = array_diff($arr1, $arr2);
        $arr1_str = print_r($arr1, true);
        $arr2_str = print_r($arr2, true);
        $diff_str = print_r($diff, true);
        if (count($diff) !== 0) {
            throw new Exception(<<<STR
                match_arrays_values: Arrays values dont match.{$msg}
                Array1: {$arr1_str}
                Array2: {$arr2_str}
                Diff:   {$diff_str}
                STR);
        }
    }

    public static function match_files(string $file_path1, string $file_path2, ?string $msg = null): void {
        $msg = isset($msg) ? "\nMessage: {$msg}" : '';
        if (!file_exists($file_path1)) {
            throw new Exception("match_files: File {$file_path1} not exists.{$msg}");
        } else if (!file_exists($file_path2)) {
            throw new Exception("match_files: File {$file_path2} not exists.{$msg}");
        }

        $contents1 = file_get_contents($file_path1);
        if ($contents1 === false) {
            throw new Exception("match_files: Unable to read {$file_path1} contents.{$msg}");
        }
        $contents2 = file_get_contents($file_path2);
        if ($contents2 === false) {
            throw new Exception("match_files: Unable to read {$file_path2} contents.{$msg}");
        }

        if ($contents1 !== $contents2) {
            throw new Exception(<<<STR
                match_files: Contents of {$file_path1} and {$file_path1} dont matching.{$msg}
                {$file_path1} contents:
                {$contents1}
                {$file_path2} contents:
                {$contents2}
                STR);
        }
    }
}
