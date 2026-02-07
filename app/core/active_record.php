<?php

namespace App\Core;

use App\Core\Helpers\Error;
use App\Core\Helpers\Log;
use Attribute;
use PDO;
use PDOException;
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
    public static ?PDO $pdo = null;

    public const string SQLITE_DNS_PREFIX = 'sqlite:';
    public const string MYSQL_DNS_PREFIX = 'mysql:';

    public static function init_connection(PDO $pdo): void {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = $pdo;
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                Log::error("init_connection(): Unable to connect to DB: {$e->getMessage()}", __FILE__, __LINE__);
                Error::internal_error();
            }
        } else {
            Log::warning('init_connection(): PDO connection already initialized');
        }
    }

    public static function sqlite_dns(string $db_file_path): string {
        return self::SQLITE_DNS_PREFIX . $db_file_path;
    }

    public static function mysql_dns(string $db_name, string $host): string {
        return self::MYSQL_DNS_PREFIX . 'dbname=' . $db_name . '; host=' . $host . '; char-set=utf8';
    }

    /**
    * @template T
    * @param class-string<\T> $class_name
    * @return ?T
    */
    public static function find_by_id(string $class_name, int $id): mixed {
        /** @var ReflectionClass<T> $r */
        $r = new ReflectionClass($class_name);
        $ar_attr = self::get_ar_attribute($r);
        Error::assert(isset($ar_attr), "find_by_id(): No ActiveRecord attribute on class {$class_name}");

        $properties = self::get_all_properties($r);
        Error::assert(isset($properties->id_attr), "find_by_id(): ID property must be set to find by id in {$class_name}");

        $stmt = self::$pdo?->prepare("select * from {$ar_attr->table_name} where {$properties->id_attr->column_name} = :id limit 1");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        $data = $stmt->fetch();
        if ($data === false) return null;

        $record = new $class_name;
        $id_name = $properties->id_field_name;
        $record->$id_name = $data[$properties->id_attr->column_name];
        foreach ($properties->props as $field_name => $field_attr) {
            $record->$field_name = $data[$field_attr->column_name];
        }

        return $record;
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
}

final class Properties {
    /**
     * @param array<string, ARField> $props
     */
    public function __construct(
        public ?ARFieldId $id_attr,
        public ?string $id_field_name,
        public array $props,
    ) {}
}
