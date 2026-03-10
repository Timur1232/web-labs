<?php

namespace App\Core\Model;

use App\Core\Helpers\Error;

/*
 * @template T
 */
interface ARModel {
    /*
     * @param class-string<T> $class_name
     * @return Error<T[], string>
     */
    function find_all(string $class_name): Error;
    /*
     * @param class-string<T> $class_name
     * @return Error<?T, string>
     */
    function find_by_id(string $class_name, mixed $id): Error;
    /*
     * @param T|T[] $class_obj
     */
    function insert(mixed $class_obj): Error;
    /*
     * @return Error<int, string>
     */
    function update_by_id(mixed $class_obj): Error;
    /*
     * @param class-string<T> $class_name
     * @return Error<int, string>
     */
    function delete_by_id(string $class_name, mixed $id): Error;
}
