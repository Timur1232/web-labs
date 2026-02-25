<?php

namespace App\Core\Model;

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
