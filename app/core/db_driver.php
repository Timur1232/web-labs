<?php

namespace App\Core;

use App\Core\Helpers\Error;
use App\Core\Helpers\Log;

final class FileSCSVDriver implements DBDriver {
    /**
     * @param array<array<string,string>> $file_contents_cache
     * @param array<int,string> $head
     */
    private function __construct(
        public string $file_path = '',
        public array $file_contents_cache = [],
        public array $head = [],
    ) {}

    public static function from(string $file_path): self {
        return new self($file_path);
    }

    public function read_all(): void {
        $handle = fopen($this->file_path, 'r');
        Error::assert($handle !== false, __METHOD__.': could not open file '.$this->file_path);
        $line = fgets($handle);
        Error::assert($line !== false, __METHOD__.": file {$this->file_path} must have head");
        $this->head = explode(';', trim($line));
        $line_n = 1;
        // TODO: proper content cache
        while (($line = fgets($handle)) !== false) {
            $line_n++;
            $splited = explode(';', trim($line));
            if (count($splited) !== count($this->head)) {
                Log::error(__METHOD__.": in file {$this->file_path}: incorrect format on line {$line_n}. Skipping");
                continue;
            }
            $this->file_contents_cache[] = array_combine($this->head, $splited);
        }
        fclose($handle);
    }

    /*
    * @return array<array<string,string[]>>
    */
    public function select(string $table_name, Properties $props, int $limit = 0): array {
        Error::todo('select not immplemented');
        $props_norm = array_flip(array_map(fn($v) => $v->column_name, $props->prop_attrs));
        if (isset($props->id_attr)) $props_norm[$props->id_attr->column_name] = $props->id_field_name;
    }

    /*
    * @return ?array<array<string,string[]>>
    */
    public function select_by_id(string $table_name, Properties $props, mixed $id_bind, int $limit = 0): array {
        if (!isset($props->id_attr)) return null;
        $ret = $this->select($table_name, $props, $limit);
        echo '<pre>';
        var_dump($ret);
        echo '</pre>';
        return array_filter($ret,
            fn($r) => $r[$props->id_attr->column_name] === strval($id_bind));
    }

    public function insert(string $table_name, Properties $props, mixed $data): bool {
        $props_norm = array_flip(array_map(fn($v) => $v->column_name,$props->prop_attrs));
        if (isset($props->id_attr)) $props_norm[$props->id_attr->column_name] = $props->id_field_name;
        $to_save = [];
        foreach ($this->head as $col) {
            $field_name = $props_norm[$col];
            isset($field_name) ? strval($data->$field_name) : '';
        }
    }

    public function save(): void {
        $handle = fopen($this->file_path, 'w');
        foreach ($this->file_contents_cache as $row) {
            $line = '';
            $i = 0;
            foreach ($row as $val) {
                $line .= $val;
                if ($i < count($this->head) - 1) $line .= ';';
                $i++;
            }
            $line .= '\n';
            fputs($handle, $line);
        }
        fclose($handle);
    }

    public function delete_by_id(string $table_name, Properties $props, mixed $id_bind): int {
        if (!isset($props->id_attr)) return -1;
        $id_col = $props->id_attr->column_name;
        if (!in_array($id_col, $this->head)) return -1;
        $size_before = count($this->file_contents_cache);
        $this->file_contents_cache =
            array_filter($this->file_contents_cache, fn($row) => $row[$id_col] === strval($id_bind));
        return $size_before - count($this->file_contents_cache);
    }

    public function update_by_id(string $table_name, Properties $props, mixed $data): bool {
        if (!isset($props->id_attr)) return false;
        $id_col = $props->id_attr->column_name;
        if (!in_array($id_col, $this->head)) return false;
        $id_field_name = $props->id_field_name;
        $size_before = count($this->file_contents_cache);
        $this->file_contents_cache =
            array_map(function ($row) use ($id_col, $id_field_name, $data) {
                if ($row[$id_col] === $data->$id_field_name) {
                    return $data;
                }
                return $row;
            }, $this->file_contents_cache);
        return true;
    }

}
