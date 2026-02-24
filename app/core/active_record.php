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
    function update_by_id(mixed $class_obj): int;
    /*
     * @template T
     * @param class-string<\T> $class_name
     */
    function delete_by_id(string $class_name, mixed $id): int;
}

interface ARQueryBuilder {
    function select(ARAttributes $props, int $limit = 0): string;
    /*
     * @param array<string,string> $bindings
     */
    function select_by_id(ARAttributes $props, int $limit = 0): string;
    /*
     * @param array<string,string> $bindings
     */
    function insert(ARAttributes $props): string;
    /*
     * @param array<string,string> $bindings
     */
    function delete_by_id(ARAttributes $props): string;
    /*
     * @param array<string,string> $bindings
     */
    function update_by_id(ARAttributes $props): string;
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

    public function get_id_column_name(): string {
        return $this->attrs[$this->id_index]->column_name;
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
