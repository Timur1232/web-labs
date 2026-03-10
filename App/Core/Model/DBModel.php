<?php

namespace App\Core\Model;

use App\Core\Model\{ARModel, ARAttributes, ARQueryBuilder};
use App\Core\Helpers\Error;
use PDO;
use Pdo\Sqlite;
/**
 * @template T
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
     * @param class-string<T> $class_name
     * @return Error<T[], string>
     */
    public function find_all(string $class_name): Error {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Error::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }

        $sql = $this->query->select($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Error::ERROR(__METHOD__.": Unable to prepare an sql statement");
        if (!$stmt->execute()) {
            return Error::ERROR(__METHOD__.': '.$this->conn->errorInfo());
        }
        $rows = $stmt->fetchAll();
        if ($rows === false) return Error::ERROR(__METHOD__.": Unable to fetch result");
        return Error::OK(array_map(fn($row) => $props->construct_obj($row), $rows));
    }

    /*
     * @param class-string<T> $class_name
     * @return Error<?T, string>
     */
    public function find_by_id(string $class_name, $id): Error {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Error::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if ($props->has_id()) {
            return Error::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        $sql = $this->query->select_by_id($props, limit: 1);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Error::ERROR(__METHOD__.": Unable to prepare an sql statement");
        $stmt->bindValue(':id', $id);
        if (!$stmt->execute()) {
            return Error::ERROR($this->conn->errorInfo());
        }
        $row = $stmt->fetch();
        if ($row === false) return Error::ERROR(__METHOD__.": Unable to fetch result");
        return Error::OK($props->construct_obj($row));
    }

    /*
     * @param T|T[] $class_obj
     */
    public function insert(mixed $class_obj): Error {
        if (is_array($class_obj) && count($class_obj) <= 0) {
            return Error::ERROR(__METHOD__.": Array must have at least one item");
        }

        $class_name = '';
        if (is_array($class_obj)) {
            $class_name = array_first($class_obj)::class;
        } else {
            $class_name = $class_obj::class;
        }
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Error::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        }

        $sql = $this->query->insert($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Error::ERROR(__METHOD__.": Unable to prepare an sql statement");
        foreach ($props->normalized() as $field => $col) {
            $stmt->bindValue(":$col", $class_obj->$field);
        }
        if (!$stmt->execute()) {
            return Error::ERROR($this->conn->errorInfo());
        }
        return Error::OK();
    }

    /*
     * @return Error<int, string>
     */
    public function update_by_id(mixed $class_obj): Error {
        $class_name = $class_obj::class;
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Error::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if ($props->has_id()) {
            return Error::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        $sql = $this->query->update_by_id($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Error::ERROR(__METHOD__.": Unable to prepare an sql statement");
        $id_field = $props->get_id_attr_norm()[0];
        $stmt->bindValue(':id', $class_obj->$id_field);
        foreach ($props->normalized() as $field => $col) {
            $stmt->bindValue(":$col", $class_obj->$field);
        }
        if (!$stmt->execute()) {
            return Error::ERROR($this->conn->errorInfo());
        }
        return Error::OK($stmt->rowCount());
    }

    /*
     * @param class-string<T> $class_name
     * @return Error<int, string>
     */
    public function delete_by_id(string $class_name, $id): Error {
        $props = ARAttributes::from($class_name);
        if (!isset($props)) {
            return Error::ERROR(__METHOD__.": No ActiveRecord attribute on class {$class_name}");
        } else if ($props->has_id()) {
            return Error::ERROR(__METHOD__.": ID property must be set to find by id in {$class_name}");
        }

        $sql = $this->query->delete_by_id($props);
        /** @var PDOStatement $stmt */
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return Error::ERROR(__METHOD__.": Unable to prepare an sql statement");
        $stmt->bindValue(':id', $id);
        if (!$stmt->execute()) {
            return Error::ERROR($this->conn->errorInfo());
        }
        return Error::OK($stmt->rowCount());
    }
}
