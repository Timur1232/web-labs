<?php
namespace App\Core\View;

use App\Core\Helpers\Error;
use Closure;

final class ComponentFunc implements Component {
    /**
     * @param Closure(): Error $comp
     */
    public function __construct(
        public \Closure $comp
    ) {}

    public function render(): Error {
        $comp_fn = $this->comp;
        return $comp_fn();
    }
}
