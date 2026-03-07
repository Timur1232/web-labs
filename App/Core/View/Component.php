<?php
namespace App\Core\View;

use App\Core\Helpers\Error;

interface Component {
    function render(): Error;
}
