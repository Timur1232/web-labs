<?php

namespace App\Core\Model;

use App\Core\Helpers\CSVFile;
use App\Core\Model\ARAttributes;
use App\Core\Model\ARModel;
use App\Core\Helpers\Result;
use App\Core\Helpers\Log;

/*
 * @template T
 * @implements ARModel<T>
 */
final class FileCSVModel implements ARModel {
    public function __construct(
        public CSVFile $csv,
    ) {}

    /*
     * @param class-string<T> $class_name
     * @return Result<self>
     */
    public static function open_or_create(string $class_name, string $file_path, string $sep = ';'): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }

        $head = array_values($props->normalized());

        if (file_exists($file_path)) {
            Log::warning(__METHOD__.": db file {$file_path} already exists. Opening it.");
            return self::open($file_path, sep: $sep, expected_head: $head);
        }

        $res = CSVFile::open_or_create($file_path, head: $head, sep: $sep);
        if (!$res->ok) return $res;
        return Result::OK(new self($res->val));
    }

    /**
     * @param string[]|null|class-string<T> $expected_head -- head for validating, pass null for no validation
     * @return Result<self>
     */
    public static function open(string $file_path, string $sep = ';', mixed $expected_head = null): Result {
        if (is_string($expected_head)) {
            $props = ARAttributes::from($expected_head);
            if (!isset($props)) {
                return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$expected_head}");
            }
            $expected_head = array_values($props->normalized());
        }
        $res = CSVFile::open($file_path, sep: $sep, expected_head: $expected_head);
        if (!$res->ok) return $res;
        return Result::OK(new self($res->val));
    }

    /*
     * @param class-string<T> $class_name
     */
    public function validate(string $class_name): bool {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return false;
        }
        foreach ($props->normalized() as $v) {
            if (!in_array($v, $this->csv->head)) {
                return false;
            }
        }
        return true;
    }

    /*
     * @param class-string<T> $class_name
     * @return Result<T[]>
     */
    public function find_all(string $class_name): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }
        $objs = [];
        foreach ($this->csv->combine_key_value() as $row) {
            $objs[] = $props->construct_obj($row);
        }
        return Result::OK($objs);
    }

    /*
     * @param class-string<T> $class_name
     * @return Result<?T>
     */
    public function find_by_id(string $class_name, $id): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if ($props->has_id()) {
            return Result::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        [$id_field_name, $id_column_name] = $props->get_id_attr_norm();
        $res = $this->csv->find([
            $id_column_name => $id
        ]);
        if (!$res->ok) return $res;

        return Result::OK(array_map(fn($v) => $props->construct_obj($v), $res->val));
    }

    /*
     * @param T|T[] $class_obj
     */
    public function insert(mixed $class_obj): Result {
        if (is_array($class_obj) && count($class_obj) <= 0) {
            return Result::ERROR(__METHOD__.": Array must have at least one item");
        }
        $class_name = '';
        if (is_array($class_obj)) {
            $class_name = array_first($class_obj)::class;
        } else {
            $class_name = $class_obj::class;
        }
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }
        if (is_array($class_obj)) {
            return $this->csv->append(array_map(fn($v) => $props->combine_columns_values($v), $class_obj));
        } else {
            return $this->csv->append([$props->combine_columns_values($class_obj)]);
        }
    }

    /*
     * @return Result<int>
     */
    public function update_by_id(mixed $class_obj): Result {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if ($props->has_id()) {
            return Result::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        [$id_field_name, $id_column_name] = $props->get_id_attr_norm();
        return $this->csv->update(
            [$id_column_name => $class_obj->$id_field_name],
            $props->combine_columns_values($class_obj),
        );
    }

    /*
     * @param class-string<T> $class_name
     * @return Result<int>
     */
    public function delete_by_id(string $class_name, $id): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if ($props->has_id()) {
            return Result::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        [$_, $id_column_name] = $props->get_id_attr_norm();
        return $this->csv->delete(
            [$id_column_name => $id],
        );
    }
}
