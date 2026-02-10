<?php

namespace App\Core;

use App\Core\Helpers\Error;
use Attribute;
use PDOStatement;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
final class ActiveRecord {
    public function __construct(
        public string $table_name,
    ) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ARField {
    public function __construct(
        public string $column_name,
    ) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ARFieldId {
    public function __construct(
        public string $column_name,
    ) {}
}

final class DB {
    // public static ?PDO $pdo = null;

    // public const string SQLITE_DNS_PREFIX = 'sqlite:';
    // public const string MYSQL_DNS_PREFIX = 'mysql:';

    // public static function init_connection(PDO $pdo): void {
    //     if (!isset(self::$pdo)) {
    //         try {
    //             self::$pdo = $pdo;
    //             self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //         } catch (PDOException $e) {
    //             Log::error(__METHOD__.": Unable to connect to DB: {$e->getMessage()}", __FILE__, __LINE__);
    //             Error::internal_error();
    //         }
    //     } else {
    //         Log::warning(__METHOD__.': PDO connection already initialized', __FILE__, __LINE__);
    //     }
    // }

    // public static function sqlite_dns(string $db_file_path): string {
    //     return self::SQLITE_DNS_PREFIX . $db_file_path;
    // }

    // public static function mysql_dns(string $db_name, string $host): string {
    //     return self::MYSQL_DNS_PREFIX . 'dbname=' . $db_name . '; host=' . $host . '; char-set=utf8';
    // }

    /**
    * @template T
    * @param class-string<\T> $class_name
    * @return ?T
    */
    public static function find_by_id(DBDriver $driver, string $class_name, mixed $id): mixed {
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_name);
        $ar_attr = self::get_ar_attribute($r);
        Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);

        $properties = self::get_all_properties($r);
        Error::assert(isset($properties->id_attr), __METHOD__.": ID property must be set to find by id in {$class_name}", __FILE__, __LINE__);

        // $cols = self::prepare_columns_wo_id($properties);
        // $cols = $properties->id_attr->column_name . $cols;

        /** @var PDOStatement $stmt */
        // $stmt = self::$pdo?->prepare("select {$cols} from {$ar_attr->table_name} where {$properties->id_attr->column_name} = :id limit 1");
        // $stmt->bindValue(':id', $id);
        // if (!$stmt->execute()) {
        //     Log::error(self::$pdo->errorInfo());
        //     return null;
        // }
        // $row = $stmt->fetch();
        $result = $driver->select_by_id($ar_attr->table_name, $properties, $id, 1);
        if ($result === null) return null;
        return self::map_columns($properties, $class_name, $result[0]);
    }

    /**
    * @template T
    * @param class-string<\T> $class_name
    * @return T[]
    */
    public static function find_all(DBDriver $driver, string $class_name): array {
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_name);
        $ar_attr = self::get_ar_attribute($r);
        Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);

        $properties = self::get_all_properties($r);
        // $cols = self::prepare_columns_wo_id($properties);
        // $cols = $properties->id_attr->column_name . $cols;

        /** @var PDOStatement $stmt */
        // $stmt = self::$pdo?->prepare("select {$cols} from {$ar_attr->table_name}");
        // if (!$stmt->execute()) {
        //     Log::error(self::$pdo->errorInfo());
        //     return [];
        // }
        // $rows = $stmt->fetchAll();

        $result = $driver->select($ar_attr->table_name, $properties);

        $records = [];
        foreach ($result as $row) {
            $records[] = self::map_columns($properties, $class_name, $row);
        }

        return $records;
    }

    /**
    * @template T
    * @param class-string<\T> $class_name
    */
    public static function delete_by_id(DBDriver $driver, string $class_name, mixed $id): int {
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_name);
        $ar_attr = self::get_ar_attribute($r);
        Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);

        $properties = self::get_all_properties($r);
        Error::assert(isset($properties->id_attr), __METHOD__.": ID property must be set to delete by id in {$class_name}", __FILE__, __LINE__);

        /** @var PDOStatement $stmt */
        // $stmt = self::$pdo?->prepare("delete from {$ar_attr->table_name} where {$properties->id_attr->column_name} = :id");
        // $stmt->bindValue(':id', $id);
        // if (!$stmt->execute()) {
        //     Log::error(self::$pdo->errorInfo());
        // }
        return $driver->delete_by_id($ar_attr, $properties, $id);
    }

    /**
    * @template T
    * @param T $class_obj
    */
    public static function insert(DBDriver $driver, mixed $class_obj): bool {
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_obj);
        $ar_attr = self::get_ar_attribute($r);
        $class_name = $class_obj::class;
        Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);

        $properties = self::get_all_properties($r);

        // $cols = self::prepare_columns_wo_id($properties);
        // $value_bindings = ':' . str_replace(', ', ', :', $cols);

        /** @var PDOStatement $stmt */
        // $stmt = self::$pdo?->prepare("insert into {$ar_attr->table_name} ({$cols}) values ({$value_bindings})");

        // if (isset($properties->id_attr)) {
        //     $id_field_name = $properties->id_field_name;
        //     $stmt->bindValue(':'.$properties->id_attr->column_name, $class_obj->$id_field_name);
        // }
        // foreach ($properties->prop_attrs as $field_name => $attr) {
        //     $stmt->bindValue(':'.$attr->column_name, $class_obj->$field_name);
        // }

        // if (!$stmt->execute()) {
        //     Log::error(self::$pdo->errorInfo());
        //     return false;
        // }
        return $driver->insert($ar_attr->table_name, $properties, $class_obj);
    }

    /**
    * @template T
    * @param T $class_obj
    */
    public static function update_by_id(DBDriver $driver, mixed $class_obj): bool {
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_obj);
        $ar_attr = self::get_ar_attribute($r);
        $class_name = $class_obj::class;
        Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);

        $properties = self::get_all_properties($r);
        Error::assert(isset($properties->id_attr), __METHOD__.": ID property must be set to update by id in {$class_name}", __FILE__, __LINE__);

        // $cols_bindings = '';
        // $comma = false;
        // foreach ($properties->prop_attrs as $prop_attr) {
        //     if ($comma) $cols_bindings = $cols_bindings . ', ';
        //     else $comma = true;
        //     $cols_bindings = $cols_bindings . $prop_attr->column_name . '= :' . $prop_attr->column_name;
        // }

        /** @var PDOStatement $stmt */
        // $stmt = self::$pdo?->prepare("update {$ar_attr->table_name} set {$cols_bindings} where {$properties->id_attr->column_name} = :id");
        // $id_field_name = $properties->id_field_name;
        // $stmt->bindValue(':id', $class_obj->$id_field_name);

        // foreach ($properties->prop_attrs as $field_name => $attr) {
        //     $stmt->bindValue(':'.$attr->column_name, $class_obj->$field_name);
        // }

        // if (!$stmt->execute()) {
        //     Log::error(self::$pdo->errorInfo());
        //     return false;
        // }
        return $driver->update_by_id($ar_attr->table_name, $properties, $class_obj);
    }

    /**
     * @template T
     * @param ReflectionClass<T> $r
     */
    private static function get_ar_attribute(ReflectionClass $r): ?ActiveRecord {
        /** @var ?ActiveRecord $ar */
        $ar = null;
        foreach ($r->getAttributes() as $attr) {
            if ($attr->getName() === ActiveRecord::class) {
                $ar = $attr->newInstance();
                break;
            }
        }
        return $ar;
    }

    /**
     * @template T
     * @param ReflectionClass<T> $r
     * TODO: come up with a good naming
     */
    private static function get_all_properties(ReflectionClass $r): Properties {
        /** @var array<string, ARField> $properties */
        $properties = [];
        /** @var ?ARFiledId $id_prop */
        $id_prop = null;
        $id_field_name = null;
        foreach ($r->getProperties() as $prop) {
            foreach ($prop->getAttributes() as $prop_attr) {
                switch ($prop_attr->getName()) {
                    case ARField::class: $properties[$prop->getName()] = $prop_attr->newInstance(); break;
                    case ARFieldId::class: {
                        $id_prop = $prop_attr->newInstance();
                        $id_field_name = $prop->getName();
                        break;
                    }
                }
            }
        }
        return new Properties($id_prop, $id_field_name, $properties);
    }

    private static function prepare_columns_wo_id(Properties $props): string {
        $columns = '';
        $first = true;
        foreach ($props->prop_attrs as $prop_attr) {
            if (!$first) $columns = $columns . ', ';
            else $first = false;
            $columns = $columns . $prop_attr->column_name;
        }
        return $columns;
    }

    /**
    * @template T
    * @param class-string<T> $class_name
    * @param array<string,string> $row
    * @return T
    */
    private static function map_columns(Properties $properties, string $class_name, array $row): mixed {
        $record = new $class_name;
        $id_name = $properties->id_field_name;
        if (isset($id_name)) {
            $record->$id_name = $row[$properties->id_attr->column_name];
        }
        foreach ($properties->prop_attrs as $field_name => $field_attr) {
            $record->$field_name = $row[$field_attr->column_name];
        }
        return $record;
    }
}

// TODO: come up with a good naming
final class Properties {
    /**
     * @param array<string, ARField> $prop_attrs
     */
    public function __construct(
        public ?ARFieldId $id_attr,
        public ?string $id_field_name,
        public array $prop_attrs,
    ) {}
}
