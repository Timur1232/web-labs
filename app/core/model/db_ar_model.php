<?php

namespace App\Core\Model;

require_once 'app/core/model/active_record.php';
require_once 'app/core/model/ar_attributes.php';

use App\Core\Model\{ARModel, ARAttributes, ARQueryBuilder};
use App\Core\Helpers\{Error, Log};
use PDO;
use Pdo\Sqlite;

final class DBModel implements ARModel {
    public function __construct(
        private PDO $conn,
        private ARQueryBuilder $query,
    ) {}

    public static function sqlite(string $db_path): self {
        return new self(new Sqlite("sqlite:{$db_path}"), new SQLiteQueryBuilder);
    }

    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return T[]
     */
    public function find_all(string $class_name): array {
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}");
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}");

        $sql = $this->query->select($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return null;
        if (!$stmt->execute()) {
            Log::error($this->conn->errorInfo());
            return null;
        }
        $rows = $stmt->fetchAll();
        if ($rows === false) return null;
        return array_map(fn($row) => $props->construct_obj($row), $rows);
    }

    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return ?T
     */
    public function find_by_id(string $class_name, $id): mixed {
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}");
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}");

        $sql = $this->query->select_by_id($props, limit: 1);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return null;
        $stmt->bindValue(':id', $id);
        if (!$stmt->execute()) {
            Log::error($this->conn->errorInfo());
            return null;
        }
        $row = $stmt->fetch();
        if ($row === false) return null;
        return $props->construct_obj($row);
    }

    public function insert(mixed $class_obj): bool {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}");

        $sql = $this->query->insert($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return null;
        foreach ($props->get_attrs_norm() as $field => $col) {
            $stmt->bindValue(":$col", $class_obj->$field);
        }
        if (!$stmt->execute()) {
            Log::error($this->conn->errorInfo());
            return false;
        }
        return true;
    }

    public function update_by_id(mixed $class_obj): int {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}");
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}");

        $sql = $this->query->update_by_id($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return null;
        $id_field = $props->get_id_attr_norm()[0];
        $stmt->bindValue(':id', $class_obj->$id_field);
        foreach ($props->get_attrs_norm() as $field => $col) {
            $stmt->bindValue(":$col", $class_obj->$field);
        }
        if (!$stmt->execute()) {
            Log::error($this->conn->errorInfo());
            return -1;
        }
        return $stmt->rowCount();
    }

    /*
     * @template T
     * @param class-string<\T> $class_name
     */
    public function delete_by_id(string $class_name, $id): int {
        $props = ARAttributes::from($class_name);
        Error::assert(isset($props), __METHOD__.": No ActiveRecord attribute on class {$class_name}");
        Error::assert($props->has_id(), __METHOD__.": ID property must be set to find by id in {$class_name}");

        $sql = $this->query->delete_by_id($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return null;
        $stmt->bindValue(':id', $id);
        if (!$stmt->execute()) {
            Log::error($this->conn->errorInfo());
            return null;
        }
        return $stmt->rowCount();
    }
}

final class SQLiteQueryBuilder implements ARQueryBuilder {
    public function select(ARAttributes $props, int $limit = 0): string {
        $cols = implode(',', $props->get_attrs_norm());
        $sql = "select {$cols} from {$props->ar_attr->table_name}";
        if ($limit > 0) $sql .= " limit {$limit}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function select_by_id(ARAttributes $props, int $limit = 0): string {
        $cols = implode(',', $props->get_attrs_norm());
        $sql = "select {$cols} from {$props->ar_attr->table_name} where {$props->get_id_column_name()} = :id";
        if ($limit > 0) $sql .= " limit {$limit}";
        return $sql;
    }

    /*
     * @param array<string,string> $bindings
     */
    public function insert(ARAttributes $props): string {
        $cols = implode(',', $props->get_attrs_norm());
        $bindings = implode(',', array_map(fn ($c) => ":$c", $props->get_attrs_norm()));
        $sql = "insert into {$props->ar_attr->table_name} ({$cols}) values ({$bindings})";
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
        $vals = implode(',', array_map(fn ($c) => "$c=:$c", $props->get_attrs_norm()));
        $sql = "update {$props->ar_attr->table_name} set {$vals} where {$props->get_id_column_name()} = :id";
        return $sql;
    }
}

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
