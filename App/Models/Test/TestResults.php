<?php
namespace App\Models\Test;

use App\Core\Helpers\Error;
use App\Core\Model\ARModel;

/*
 * @template T
 * @implements ARModel<T>
 */
final class TestResults implements ARModel {
    /*
     * @param class-string<T> $class_name
     * @return Error<T[], string>
     */
    function find_all(string $class_name): Error {
    }

    /*
     * @param class-string<T> $class_name
     * @return Error<?T, string>
     */
    function find_by_id(string $class_name, mixed $id): Error {
    }

    function insert(mixed $class_obj): Error {
    }

    /*
     * @return Error<int, string>
     */
    function update_by_id(mixed $class_obj): Error {
    }

    /*
     * @param class-string<T> $class_name
     * @return Error<int, string>
     */
    function delete_by_id(string $class_name, mixed $id): Error {
    }
}


