<?php

namespace App\Core\Model;

use Attribute;

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

