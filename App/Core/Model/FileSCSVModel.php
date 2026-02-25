<?php

namespace App\Models;

use App\Core\Model\ARAttributes;
use App\Core\Model\ARModel;
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use Generator;

final class FileSCSVModel implements ARModel {

    /*
     * @param string[] $head
     * @param string[][] $contents
     */
    public function __construct(
        public string $file_path,
        public array $head = [],
        private array $contents = [],
    ) {}

    public static function open(string $file_path): ?self {
        if (!file_exists($file_path)) return null;
        return new self($file_path);
    }

    public function read_all(): void {
        $handle = fopen($this->file_path, 'r');
        Error::assert($handle !== false, __METHOD__.": error opening file {$this->file_path}");
        $line = fgets($handle);
        if ($line === false) {
            fclose($handle);
            Error::assert(false, __METHOD__.": file {$this->file_path} must have head");
        }
        $this->head = explode(';', trim($line));
        // TODO: error handling
        // if ($this->head === false) return null;
        $line_n = 1;
        while (($line = fgets($handle)) !== false) {
            $line_n++;
            $row = $this->parse_line($line);
            if ($row === null) {
                Log::error(__METHOD__.": in file {$this->file_path}: incorrect format on line {$line_n}. Skipping.");
                continue;
            }
            $this->contents[] = $row;
        }
        fclose($handle);
    }
    /**
     * @return Generator<array<string, string>>
     */
    private function combine_norm(): iterable {
        foreach ($this->contents as $data) {
            yield array_combine($this->head, $data);
        }
    }

    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return T[]
     */
    public function find_all(string $class_name): array {
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
        $objs = [];
        foreach ($this->combine_norm() as $data) {
            $objs[] = $props->construct_obj($data);
        }
        return $objs;
    }

    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return ?T
     */
    public function find_by_id(string $class_name, $id): mixed {
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}", __FILE__, __LINE__);
        [$id_field_name, $id_column_name] = $props->get_id_attr_norm();
        foreach ($this->combine_norm() as $data) {
            // WARNING: loosy-goosy-ass compare
            if ($data[$id_column_name] == $id) {
                return $props->construct_obj($data);
            }
        }
        return null;
    }

    public function insert(mixed $class_obj): bool {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
        $line = $this->serialize($class_obj, $props);
        $handle = fopen($this->file_path, 'a');
        if ($handle === false) return false;
        if (fputs($handle, $line) === false) return false;
        fclose($handle);
        $this->contents[] = $this->parse_line($line);
        return true;
    }

    public function update_by_id(mixed $class_obj): int {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}", __FILE__, __LINE__);
        [$id_field_name, $id_column_name] = $props->get_id_attr_norm();

        $line = $this->serialize($class_obj, $props);
        $handle = fopen($this->file_path, 'w');
        if ($handle === false) return -1;
        fputs($handle, implode(';', $this->head)."\n");

        $i = 0;
        $count = 0;
        foreach ($this->combine_norm() as $data) {
            // WARNING: loosy-goosy-ass compare
            if ($data[$id_column_name] == $class_obj->$id_field_name) {
                fputs($handle, $line);
                $this->contents[$i] = $this->parse_line($line);
                $count++;
            } else {
                fputs($handle, implode(';', $data)."\n");
            }
            $i++;
        }

        fclose($handle);
        return $count;
    }

    /*
     * @template T
     * @param class-string<\T> $class_name
     */
    public function delete_by_id(string $class_name, $id): int {
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}", __FILE__, __LINE__);
        [$id_field_name, $id_column_name] = $props->get_id_attr_norm();

        $handle = fopen($this->file_path, 'w');
        if ($handle === false) return -1;
        fputs($handle, implode(';', $this->head)."\n");

        $i = 0;
        $count = 0;
        foreach ($this->combine_norm() as $data) {
            // WARNING: loosy-goosy-ass compare
            if ($data[$id_column_name] != $id) {
                fputs($handle, implode(';', $data)."\n");
            } else {
                array_splice($this->contents, $i, 1);
                $count++;
            }
            $i++;
        }

        fclose($handle);
        return $count;
    }

    /*
    * @return ?string[]
    */
    private function parse_line(string $line): ?array {
        $splited = explode(';', trim($line));
        if ($splited === false) return null;
        if (count($splited) !== count($this->head)) return null;
        return $splited;
    }

    private function serialize(mixed $class_obj, ARAttributes $props): string {
        $props_norm = array_flip($props->get_attrs_norm());
        $line = '';
        $i = 0;
        foreach ($this->head as $col) {
            if (array_key_exists($col, $props_norm)) {
                $field_name = $props_norm[$col];
                $line .= strval($class_obj->$field_name);
            }
            if ($i < count($this->head) - 1) {
                $line .= ';';
            }
            $i++;
        }
        return $line . "\n";
    }
}
