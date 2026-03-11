<?php
namespace App\Core\View;

use App\Core\Helpers\Result;

interface Component {
    function render(): Result;
}
