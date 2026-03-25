<?php

namespace App\Core\Model;

use App\Core\Model\{ARModel, ARAttributes, ARQueryBuilder};
use App\Core\Helpers\Result;
use PDO;
use Pdo\Sqlite;

/**
 * @implements ARModel<T>
 */
final class DBModel implements ARModel {
    public function __construct(
        private PDO $conn,
        private ARQueryBuilder $query,
    ) {}

    public static function sqlite(string $db_path): self {
        return new self(new Sqlite("sqlite:{$db_path}"), new SQLiteQueryBuilder());
    }

    /*
     * @template T
     * @param class-string<T> $class_name
     * @return Result<T[]>
     */
    public function find_all(string $class_name, int $limit = 0): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }

        $sql = $this->query->select($props, limit: $limit);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Result::ERROR(__METHOD__.": Unable to prepare an sql statement");
        if (!$stmt->execute()) {
            return Result::ERROR(__METHOD__.': '.$this->conn->errorInfo());
        }
        $rows = $stmt->fetchAll();
        if ($rows === false) return Result::ERROR(__METHOD__.": Unable to fetch result");
        return Result::OK(array_map(fn($row) => $props->construct_obj($row), $rows));
    }

    /*
     * @template T
     * @param class-string<T> $class_name
     * @return Result<?T>
     */
    public function find_by_id(string $class_name, $id, int $limit = 1): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if (!$props->has_id()) {
            return Result::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        $sql = $this->query->select_by_id($props, limit: $limit);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Result::ERROR(__METHOD__.": Unable to prepare an sql statement");
        $stmt->bindValue(':id', $id);
        if (!$stmt->execute()) {
            return Result::ERROR($this->conn->errorInfo()[2]);
        }
        $row = $stmt->fetch();
        if ($row === false) return Result::ERROR(__METHOD__.": Unable to fetch result");
        return Result::OK($props->construct_obj($row));
    }

    /*
     * @template T
     * @param T|T[] $class_obj
     */
    public function insert(mixed $class_obj): Result {
        if (is_array($class_obj) && count($class_obj) <= 0) {
            return Result::ERROR(__METHOD__.": Array must have at least one item");
        }

        $class_name = '';
        $count = 1;
        if (is_array($class_obj)) {
            $class_name = array_first($class_obj)::class;
            $count = count($class_obj);
        } else {
            $class_name = $class_obj::class;
        }
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }

        $sql = $this->query->insert($props, $count);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Result::ERROR(__METHOD__.": Unable to prepare an sql statement");
        if (is_array($class_obj)) {
            $i = 0;
            foreach ($class_obj as $obj) {
                foreach ($props->normalized() as $field => $col) {
                    $stmt->bindValue(":{$col}{$i}", $obj->$field);
                }
                $i++;
            }
        } else {
            foreach ($props->normalized() as $field => $col) {
                $stmt->bindValue(":{$col}0", $class_obj->$field);
            }
        }
        if (!$stmt->execute()) {
            return Result::ERROR($this->conn->errorInfo()[2]);
        }
        return Result::OK();
    }

    /*
     * @return Result<int>
     */
    public function update_by_id(mixed $class_obj): Result {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if (!$props->has_id()) {
            return Result::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        $sql = $this->query->update_by_id($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Result::ERROR(__METHOD__.": Unable to prepare an sql statement");
        $id_field = $props->get_id_attr_norm()[0];
        $stmt->bindValue(':id', $class_obj->$id_field);
        foreach ($props->normalized() as $field => $col) {
            $stmt->bindValue(":$col", $class_obj->$field);
        }
        if (!$stmt->execute()) {
            return Result::ERROR($this->conn->errorInfo()[2]);
        }
        return Result::OK($stmt->rowCount());
    }

    /*
     * @template T
     * @param class-string<T> $class_name
     * @return Result<int>
     */
    public function delete_by_id(string $class_name, $id): Result {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Result::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if (!$props->has_id()) {
            return Result::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        $sql = $this->query->delete_by_id($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Result::ERROR(__METHOD__.": Unable to prepare an sql statement");
        $stmt->bindValue(':id', $id);
        if (!$stmt->execute()) {
            return Result::ERROR($this->conn->errorInfo()[2]);
        }
        return Result::OK($stmt->rowCount());
    }
}
