<?php
namespace App\Models\Test;

use App\Core\Helpers\Result;
use App\Core\Model\ARModel;

/*
 * @template T
 * @implements ARModel<T>
 */
final class TestResults implements ARModel {
    /*
     * @param class-string<T> $class_name
     * @return Result<T[]>
     */
    function find_all(string $class_name): Result {
    }

    /*
     * @param class-string<T> $class_name
     * @return Result<?T>
     */
    function find_by_id(string $class_name, mixed $id): Result {
    }

    function insert(mixed $class_obj): Result {
    }

    /*
     * @return Result<int>
     */
    function update_by_id(mixed $class_obj): Result {
    }

    /*
     * @param class-string<T> $class_name
     * @return Result<int>
     */
    function delete_by_id(string $class_name, mixed $id): Result {
    }
}


