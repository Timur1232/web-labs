<?php

namespace App\Core\Model;

interface ARQueryBuilder {
    function select(ARAttributes $props, int $limit = 0): string;
    /*
     * @param array<string,string> $bindings
     */
    function select_by_id(ARAttributes $props, int $limit = 1): string;
    /*
     * @param array<string,string> $bindings
     */
    function insert(ARAttributes $props, int $count = 1): string;
    /*
     * @param array<string,string> $bindings
     */
    function delete_by_id(ARAttributes $props): string;
    /*
     * @param array<string,string> $bindings
     */
    function update_by_id(ARAttributes $props): string;
}

