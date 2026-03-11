<?php

namespace App\Core\Model;

use App\Core\Helpers\Result;

/*
 * @template T
 */
interface ARModel {
    /*
     * @param class-string<T> $class_name
     * @return Result<T[]>
     */
    function find_all(string $class_name): Result;
    /*
     * @param class-string<T> $class_name
     * @return Result<?T>
     */
    function find_by_id(string $class_name, mixed $id): Result;
    /*
     * @param T|T[] $class_obj
     */
    function insert(mixed $class_obj): Result;
    /*
     * @return Result<int>
     */
    function update_by_id(mixed $class_obj): Result;
    /*
     * @param class-string<T> $class_name
     * @return Result<int>
     */
    function delete_by_id(string $class_name, mixed $id): Result;
}
