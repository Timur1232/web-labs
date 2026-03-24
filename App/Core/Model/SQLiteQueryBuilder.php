<?php

namespace App\Core\Model;

final class SQLiteQueryBuilder implements ARQueryBuilder {
    public function select(ARAttributes $props, int $limit = 0): string {
        $cols = implode(',', $props->normalized());
        $sql = "select {$cols} from {$props->ar_attr->table_name}";
        if ($limit > 0) $sql .= " limit {$limit}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function select_by_id(ARAttributes $props, int $limit = 1): string {
        $cols = implode(',', $props->normalized());
        $sql = "select {$cols} from {$props->ar_attr->table_name} where {$props->get_id_column_name()} = :id";
        if ($limit > 0) $sql .= " limit {$limit}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function insert(ARAttributes $props, int $count = 1): string {
        $norm = $props->normalized();
        $cols = implode(',', $norm);
        $values = '';
        foreach (range(0, $count-1) as $i) {
            $bindings = implode(',', array_map(fn ($c) => ":{$c}{$i}", $norm));
            $values .= "({$bindings})";
            if ($i < $count-1) $values .= ',';
        }
        $sql = "insert into {$props->ar_attr->table_name} ({$cols}) values {$values}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function delete_by_id(ARAttributes $props): string {
        $sql = "delete from {$props->ar_attr->table_name} where {$props->get_id_column_name()} = :id";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function update_by_id(ARAttributes $props): string {
        $vals = implode(',', array_map(fn ($c) => "$c=:$c", $props->normalized()));
        $sql = "update {$props->ar_attr->table_name} set {$vals} where {$props->get_id_column_name()} = :id";
        return $sql;
    }
}
