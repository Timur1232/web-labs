<?php

namespace App\Core\Model;

use App\Core\Helpers\Log;

final class MySQLQueryBuilder implements ARQueryBuilder {
    public function select(ARAttributes $props, int $limit = 0): string {
        Log::warning("mysql select: not tested");
        $cols = implode(',', $props->get_attrs_norm());
        $sql = "select {$cols} from {$props->ar_attr->table_name}";
        if ($limit > 0) $sql .= " limit {$limit}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function select_by_id(ARAttributes $props, int $limit = 0): string {
        Log::warning("mysql select_by_id: not tested");
        $cols = implode(',', $props->get_attrs_norm());
        $sql = "select {$cols} from {$props->ar_attr->table_name} where {$props->get_id_column_name()} = :id";
        if ($limit > 0) $sql .= " limit {$limit}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function insert(ARAttributes $props): string {
        Log::warning("mysql insert: not tested");
        $cols = implode(',', $props->get_attrs_norm());
        $bindings = implode(',', array_map(fn ($c) => ":$c", $props->get_attrs_norm()));
        $sql = "insert into {$props->ar_attr->table_name} ({$cols}) values ({$bindings})";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function delete_by_id(ARAttributes $props): string {
        Log::warning("mysql delete_by_id: not tested");
        $sql = "delete from {$props->ar_attr->table_name} where {$props->get_id_column_name()} = :id";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function update_by_id(ARAttributes $props): string {
        Log::warning("mysql update_by_id: not tested");
        $vals = implode(',', array_map(fn ($c) => "$c=:$c", $props->get_attrs_norm()));
        $sql = "update {$props->ar_attr->table_name} set {$vals} where {$props->get_id_column_name()} = :id";
        return $sql;
    }
}
