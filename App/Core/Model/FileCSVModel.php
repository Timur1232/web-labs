<?php

namespace App\Core\Model;

use App\Core\Model\ARAttributes;
use App\Core\Model\ARModel;
use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use Generator;

final class FileCSVModel implements ARModel {

    /*
     * @param string[] $head
     * @param string[][] $contents
     */
    public function __construct(
        public string $file_path,
        public array $head = [],
        public string $sep = ';',
        public array $contents = [],
    ) {}

    /*
     * @template T
     * @param class-string<\T> $class_name
     */
    public static function create_db(string $class_name, string $file_path, string $sep = ';'): ?self {
        if (file_exists($file_path)) {
            Log::warning(__METHOD__.": db file {$file_path} already exists. Opening it.");
            return self::open($file_path, $sep);
        }
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
        $self = new self($file_path, sep: $sep);
        $self->head = array_keys($props->get_attrs_norm());

        $handle = fopen($self->file_path, 'w');
        if ($handle === false) return null;
        if (fputs($handle, implode($self->sep, $self->head)."\n") === false) return null;
        if (!fclose($handle)) return null;

        return $self;
    }

    public static function open(string $file_path, string $sep = ';'): ?self {
        $handle = fopen($file_path, 'r');
        if ($handle === false) {
            Log::error(__METHOD__.": file not exist {$file_path}");
            return null;
        }
        $self = new self(file_path: $file_path, sep: $sep);
        $self->head = $self->read_head($handle);
        if (!isset($self->head)) {
            fclose($handle);
            return null;
        }
        $self->read_content($handle);
        fclose($handle);
        return $self;
    }
    /**
     * @param recource $handle
     * @return ?string[]
     */
    private function read_head($handle): ?array {
        $line = fgets($handle);
        if ($line === false) {
            Log::error(__METHOD__.": file {$this->file_path} must have head");
            return null;
        }
        $head = explode($this->sep, trim($line));
        if ($head === false) {
            Log::error(__METHOD__.": unable to read file head in {$this->file_path}");
            return null;
        }
        return $head;
    }
    /**
     * @param recource $handle
     */
    private function read_content($handle): bool {
        if (!isset($this->head)) {
            return false;
        }
        $line_n = 1;
        while (($line = fgets($handle)) !== false) {
            $line_n++;
            $row = $this->parse_line($line);
            if ($row === null) {
                Log::error(__METHOD__.": {$this->file_path}:{$line_n}: incorrect format. Skipping.");
                continue;
            }
            $this->contents[] = $row;
        }
        return true;
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
        fputs($handle, implode($this->sep, $this->head)."\n");

        $i = 0;
        $count = 0;
        foreach ($this->combine_norm() as $data) {
            // WARNING: loosy-goosy-ass compare
            if ($data[$id_column_name] == $class_obj->$id_field_name) {
                fputs($handle, $line);
                $this->contents[$i] = $this->parse_line($line);
                $count++;
            } else {
                fputs($handle, implode($this->sep, str_replace($this->sep, "\\{$this->sep}", $data))."\n");
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
        fputs($handle, implode($this->sep, $this->head)."\n");

        $i = 0;
        $count = 0;
        foreach ($this->combine_norm() as $data) {
            // WARNING: loosy-goosy-ass compare
            if ($data[$id_column_name] != $id) {
                fputs($handle, implode($this->sep, str_replace($this->sep, "\\{$this->sep}", $data))."\n");
            } else {
                array_splice($this->contents, $i, 1);
                $count++;
            }
            $i++;
        }

        fclose($handle);
        return $count;
    }
    /**
     * @return string[]
     */
    private function split_escaped(string $str): array {
        $acc = '';
        $res = [];
        $escape = false;
        foreach (mb_str_split($str, encoding: 'UTF-8') as $ch) {
            if ($ch === '\\') {
                $escape = true;
            } else if ((!$escape && $ch !== $this->sep) || ($escape && $ch === $this->sep)) {
                $acc .= $ch;
                $escape = false;
            } else {
                $res[] = $acc;
                $acc = '';
            }
        }
        $res[] = $acc;
        return $res;
    }

    /*
    * @return ?string[]
    */
    private function parse_line(string $line): ?array {
        $splited = $this->split_escaped(trim($line));
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
                $line .= str_replace($this->sep, "\\{$this->sep}", strval($class_obj->$field_name));
            }
            if ($i < count($this->head) - 1) {
                $line .= $this->sep;
            }
            $i++;
        }
        return $line . "\n";
    }

}
