<?php namespace App\Models\Common_Sql;
use App\Core\Helpers\Error;
use App\Core\Model\AR_Reflect;

enum Order_Dir: string {
    case ASC = 'asc';
    case DESC = 'desc';
}

final class Order {
    public function __construct(
        public string $column,
        public Order_Dir $direction,
    ) {}

    public static function asc(string $column): self {
        return new self($column, Order_Dir::ASC);
    }

    public static function desc(string $column): self {
        return new self($column, Order_Dir::DESC);
    }
}

final class Common_Sql {
    public static function select(string|array $source, ?string $table = null, ?string $where = null, ?Order $order_by = null): string {
        if (is_array($source) && $table === null) {
            Error::assert(false, __METHOD__.': Table name is required when passing an array of columns.');
        }

        self::resolve_table_and_columns($source, $table, $columns);

        $column_list = implode(', ', $columns);
        $sql = "select {$column_list} from {$table}";

        if (!is_null($where)) {
            $sql .= " where {$where}";
        }

        if (!is_null($order_by)) {
            $sql .= ' order by ' . $order_by->column . ' ' . $order_by->direction->value;
        }

        return $sql;
    }

    public static function insert(string|array $source, ?string $table = null, int $count = 1): string {
        if (is_array($source) && $table === null) {
            Error::assert(false, __METHOD__.': Table name is required when passing an array of columns.');
        }

        $columns = [];
        self::resolve_table_and_columns($source, $table, $columns);

        $column_list = implode(', ', $columns);
        $binding_list = '';
        if ($count === 1) {
            $binding_list = '(' . implode(', ', array_map(fn(string $col) => ":{$col}", $columns)) . ')';
        } else {
            for ($i = 0; $i < $count; $i += 1) {
                $binding_list .= '(' . implode(', ', array_map(fn(string $col) => ":{$col}{$i}", $columns)) . ')';
                if ($i < $count-1) {
                    $binding_list .= ', ';
                }
            }
        }

        return "insert into {$table} ({$column_list}) values {$binding_list}";
    }

    public static function update(string|array $source, ?string $table = null, ?string $where = null): string {
        if (is_array($source) && $table === null) {
            Error::assert(false, __METHOD__.': Table name is required when passing an array of columns.');
        }

        $columns = [];
        self::resolve_table_and_columns($source, $table, $columns);

        $set_parts = implode(', ', array_map(fn(string $col) => "{$col} = :{$col}", $columns));

        $sql = "update {$table} set {$set_parts}";

        if (!is_null($where)) {
            $sql .= " where {$where}";
        }

        return $sql;
    }

    public static function delete(string $table, string $where): string {
        $sql = "delete from {$table}";
        $sql .= " where {$where}";
        return $sql;
    }

    private function __construct() {}

    private static function resolve_table_and_columns(string|array $source, ?string &$table, ?array &$columns): void {
        if (is_string($source)) {
            $reflect = AR_Reflect::from($source);
            if ($reflect === null) {
                Error::assert(false, __METHOD__.": Class {$source} does not have #[Active_Record] attribute.");
            }
            $table = $reflect->ar_attr->table_name;
            $mapping = $reflect->fields_to_columns_array();
            $columns = array_values($mapping);
        } elseif (is_array($source)) {
            $columns = $source;
        } else {
            Error::assert(false, __METHOD__.': Source must be a class-string or an array of column names.');
        }
    }
}
