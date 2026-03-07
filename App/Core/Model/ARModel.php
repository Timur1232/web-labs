<?php

namespace App\Core\Model;

use App\Core\Helpers\Error;

interface ARModel {
    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return Error<T[]>
     */
    function find_all(string $class_name): Error;
    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return Error<?T>
     */
    function find_by_id(string $class_name, mixed $id): Error;
    function insert(mixed $class_obj): Error;
    /*
     * @return Error<int>
     */
    function update_by_id(mixed $class_obj): Error;
    /*
     * @template T
     * @param class-string<\T> $class_name
     * @return Error<int>
     */
    function delete_by_id(string $class_name, mixed $id): Error;
}
