<?php

namespace App\Core;

use Attribute;
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
        public int $flags = 0,
    ) {}

    public const ID_FIELD = 0x1 << 0;
}

interface ARModel {
    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return T[]
     */
    function find_all(string $class_name): array;
    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return ?T
     */
    function find_by_id(string $class_name, mixed $id): mixed;
    function insert(mixed $class_obj): bool;
    function update_by_id(mixed $class_obj): bool;
    /*
     * @template T
     * @param class-string<\T> $class_name
     */
    function delete_by_id(string $class_name, mixed $id): int;
}

interface ARConnection {
    function close(): void;
    function execute(string $query): void;
    function fetch(): mixed;
    /*
     * @return mixed[]
     */
    function fetch_all(): array;
}

interface ARQueryBuilder {
    function select(string $table_name, ARAttributes $props, int $limit = 0): string;
    function select_by_id(string $table_name, ARAttributes $props, mixed $id_bind, int $limit = 0): string;
    function insert(string $table_name, ARAttributes $props, mixed $data): string;
    function delete_by_id(string $table_name, ARAttributes $props, mixed $id_bind): string;
    function update_by_id(string $table_name, ARAttributes $props, mixed $data): string;
}

final class ARAttributes {
    /*
     * @template T
     * @param array<string, ARField> $attrs
     * @param class-string<\T> $class_name
     */
    public function __construct(
        public ActiveRecord $ar_attr,
        public string $class_name,
        public array $attrs = [],
        public ?string $id_index = null,
    ) {}

    /*
     * @var array<string, ARAttributes> $reflection_cache
     */
    public static array $reflection_cache = [];

    /*
     * @template T
     * @param class-string<\T> $class_name
     */
    public static function from(string $class_name): ?self {
        if (array_key_exists($class_name, self::$reflection_cache)) return self::$reflection_cache[$class_name];
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_name);
        $ar_attr = self::get_ar_attribute($r);
        if (!isset($ar_attr)) return null;
        $self = new self($ar_attr, $class_name);
        foreach ($r->getProperties() as $prop) {
            foreach ($prop->getAttributes() as $prop_attr) {
                if ($prop_attr->getName() === ARField::class) {
                    $attr = $prop_attr->newInstance();
                    if ($attr->flags & ARField::ID_FIELD !== 0) {
                        $self->id_index = $prop->getName();
                    }
                    $self->attrs[$prop->getName()] = $attr;
                }
            }
        }
        self::$reflection_cache[$class_name] = $self;
        return $self;
    }

    public function get_table_name(): string {
        return $this->ar_attr->table_name;
    }

    /*
     * @return array<string, string>
     */
    public function get_attrs_norm(): array {
        return array_map(fn($v) => $v->column_name, $this->attrs);
    }

    public function has_id(): bool {
        return isset($this->id_index);
    }

    /*
    * @return array[string, ARField]
    */
    public function get_id_attr(): array {
        return [$this->id_index, $this->attrs[$this->id_index]];
    }

    /*
    * @return array[string, string]
    */
    public function get_id_attr_norm(): array {
        return [$this->id_index, $this->attrs[$this->id_index]->column_name];
    }

    /*
     * @return array<string, string>
     */
    public function normalized(): array {
        return array_map(fn($v) => $v->column_name, $this->attrs);
    }

    /*
     * @template T
     * @param array<string, mixed> $data
     * @return T
     */
    public function construct_obj(array $data): mixed {
        $obj = new $this->class_name;
        foreach ($this->normalized() as $field_name => $column_name) {
            $obj->$field_name = $data[$column_name];
        }
        return $obj;
    }

    /*
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
}

// final class DB {
//     // public static ?PDO $pdo = null;
// 
//     // public const string SQLITE_DNS_PREFIX = 'sqlite:';
//     // public const string MYSQL_DNS_PREFIX = 'mysql:';
// 
//     // public static function init_connection(PDO $pdo): void {
//     //     if (!isset(self::$pdo)) {
//     //         try {
//     //             self::$pdo = $pdo;
//     //             self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//     //         } catch (PDOException $e) {
//     //             Log::error(__METHOD__.": Unable to connect to DB: {$e->getMessage()}", __FILE__, __LINE__);
//     //             Error::internal_error();
//     //         }
//     //     } else {
//     //         Log::warning(__METHOD__.': PDO connection already initialized', __FILE__, __LINE__);
//     //     }
//     // }
// 
//     // public static function sqlite_dns(string $db_file_path): string {
//     //     return self::SQLITE_DNS_PREFIX . $db_file_path;
//     // }
// 
//     // public static function mysql_dns(string $db_name, string $host): string {
//     //     return self::MYSQL_DNS_PREFIX . 'dbname=' . $db_name . '; host=' . $host . '; char-set=utf8';
//     // }
// 
//     /**
//     * @template T
//     * @param class-string<\T> $class_name
//     * @return ?T
//     */
//     public static function find_by_id(DBDriver $driver, string $class_name, mixed $id): mixed {
//         /** @var ReflectionClass<T> $r */
//         $r = new ReflectionClass($class_name);
//         $ar_attr = self::get_ar_attribute($r);
//         Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
// 
//         $properties = self::get_all_properties($r);
//         Error::assert(isset($properties->id_attr), __METHOD__.": ID property must be set to find by id in {$class_name}", __FILE__, __LINE__);
// 
//         // $cols = self::prepare_columns_wo_id($properties);
//         // $cols = $properties->id_attr->column_name . $cols;
// 
//         /** @var PDOStatement $stmt */
//         // $stmt = self::$pdo?->prepare("select {$cols} from {$ar_attr->table_name} where {$properties->id_attr->column_name} = :id limit 1");
//         // $stmt->bindValue(':id', $id);
//         // if (!$stmt->execute()) {
//         //     Log::error(self::$pdo->errorInfo());
//         //     return null;
//         // }
//         // $row = $stmt->fetch();
//         $result = $driver->select_by_id($ar_attr->table_name, $properties, $id, 1);
//         if ($result === null) return null;
//         return self::map_columns($properties, $class_name, $result[0]);
//     }
// 
//     /**
//     * @template T
//     * @param class-string<\T> $class_name
//     * @return T[]
//     */
//     public static function find_all(DBDriver $driver, string $class_name): array {
//         /** @var ReflectionClass<T> $r */
//         $r = new ReflectionClass($class_name);
//         $ar_attr = self::get_ar_attribute($r);
//         Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
// 
//         $properties = self::get_all_properties($r);
//         // $cols = self::prepare_columns_wo_id($properties);
//         // $cols = $properties->id_attr->column_name . $cols;
// 
//         /** @var PDOStatement $stmt */
//         // $stmt = self::$pdo?->prepare("select {$cols} from {$ar_attr->table_name}");
//         // if (!$stmt->execute()) {
//         //     Log::error(self::$pdo->errorInfo());
//         //     return [];
//         // }
//         // $rows = $stmt->fetchAll();
// 
//         $result = $driver->select($ar_attr->table_name, $properties);
// 
//         $records = [];
//         foreach ($result as $row) {
//             $records[] = self::map_columns($properties, $class_name, $row);
//         }
// 
//         return $records;
//     }
// 
//     /**
//     * @template T
//     * @param class-string<\T> $class_name
//     */
//     public static function delete_by_id(DBDriver $driver, string $class_name, mixed $id): int {
//         /** @var ReflectionClass<T> $r */
//         $r = new ReflectionClass($class_name);
//         $ar_attr = self::get_ar_attribute($r);
//         Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
// 
//         $properties = self::get_all_properties($r);
//         Error::assert(isset($properties->id_attr), __METHOD__.": ID property must be set to delete by id in {$class_name}", __FILE__, __LINE__);
// 
//         /** @var PDOStatement $stmt */
//         // $stmt = self::$pdo?->prepare("delete from {$ar_attr->table_name} where {$properties->id_attr->column_name} = :id");
//         // $stmt->bindValue(':id', $id);
//         // if (!$stmt->execute()) {
//         //     Log::error(self::$pdo->errorInfo());
//         // }
//         return $driver->delete_by_id($ar_attr, $properties, $id);
//     }
// 
//     /**
//     * @template T
//     * @param T $class_obj
//     */
//     public static function insert(DBDriver $driver, mixed $class_obj): bool {
//         /** @var ReflectionClass<T> $r */
//         $r = new ReflectionClass($class_obj);
//         $ar_attr = self::get_ar_attribute($r);
//         $class_name = $class_obj::class;
//         Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
// 
//         $properties = self::get_all_properties($r);
// 
//         // $cols = self::prepare_columns_wo_id($properties);
//         // $value_bindings = ':' . str_replace(', ', ', :', $cols);
// 
//         /** @var PDOStatement $stmt */
//         // $stmt = self::$pdo?->prepare("insert into {$ar_attr->table_name} ({$cols}) values ({$value_bindings})");
// 
//         // if (isset($properties->id_attr)) {
//         //     $id_field_name = $properties->id_field_name;
//         //     $stmt->bindValue(':'.$properties->id_attr->column_name, $class_obj->$id_field_name);
//         // }
//         // foreach ($properties->prop_attrs as $field_name => $attr) {
//         //     $stmt->bindValue(':'.$attr->column_name, $class_obj->$field_name);
//         // }
// 
//         // if (!$stmt->execute()) {
//         //     Log::error(self::$pdo->errorInfo());
//         //     return false;
//         // }
//         return $driver->insert($ar_attr->table_name, $properties, $class_obj);
//     }
// 
//     /**
//     * @template T
//     * @param T $class_obj
//     */
//     public static function update_by_id(DBDriver $driver, mixed $class_obj): bool {
//         /** @var ReflectionClass<T> $r */
//         $r = new ReflectionClass($class_obj);
//         $ar_attr = self::get_ar_attribute($r);
//         $class_name = $class_obj::class;
//         Error::assert(isset($ar_attr), __METHOD__.": No ActiveRecord attribute on class {$class_name}", __FILE__, __LINE__);
// 
//         $properties = self::get_all_properties($r);
//         Error::assert(isset($properties->id_attr), __METHOD__.": ID property must be set to update by id in {$class_name}", __FILE__, __LINE__);
// 
//         // $cols_bindings = '';
//         // $comma = false;
//         // foreach ($properties->prop_attrs as $prop_attr) {
//         //     if ($comma) $cols_bindings = $cols_bindings . ', ';
//         //     else $comma = true;
//         //     $cols_bindings = $cols_bindings . $prop_attr->column_name . '= :' . $prop_attr->column_name;
//         // }
// 
//         /** @var PDOStatement $stmt */
//         // $stmt = self::$pdo?->prepare("update {$ar_attr->table_name} set {$cols_bindings} where {$properties->id_attr->column_name} = :id");
//         // $id_field_name = $properties->id_field_name;
//         // $stmt->bindValue(':id', $class_obj->$id_field_name);
// 
//         // foreach ($properties->prop_attrs as $field_name => $attr) {
//         //     $stmt->bindValue(':'.$attr->column_name, $class_obj->$field_name);
//         // }
// 
//         // if (!$stmt->execute()) {
//         //     Log::error(self::$pdo->errorInfo());
//         //     return false;
//         // }
//         return $driver->update_by_id($ar_attr->table_name, $properties, $class_obj);
//     }
// 
//     private static function prepare_columns_wo_id(Properties $props): string {
//         $columns = '';
//         $first = true;
//         foreach ($props->prop_attrs as $prop_attr) {
//             if (!$first) $columns = $columns . ', ';
//             else $first = false;
//             $columns = $columns . $prop_attr->column_name;
//         }
//         return $columns;
//     }
// 
//     /**
//     * @template T
//     * @param class-string<T> $class_name
//     * @param array<string,string> $row
//     * @return T
//     */
//     private static function map_columns(Properties $properties, string $class_name, array $row): mixed {
//         $record = new $class_name;
//         $id_name = $properties->id_field_name;
//         if (isset($id_name)) {
//             $record->$id_name = $row[$properties->id_attr->column_name];
//         }
//         foreach ($properties->prop_attrs as $field_name => $field_attr) {
//             $record->$field_name = $row[$field_attr->column_name];
//         }
//         return $record;
//     }
// }

