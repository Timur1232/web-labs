<?php
namespace App\Core\View;

use App\Core\Helpers\Result;
use Closure;

final class ComponentFunc implements Component {
    /**
     * @param Closure(): Result $comp
     */
    public function __construct(
        public Closure $comp
    ) {}

    /**
     * @param Closure(): Result $comp
     */
    public static function from(Closure $comp): self {
        return new self(comp: $comp);
    }

    public function render(): Result {
        $comp_fn = $this->comp;
        return $comp_fn();
    }
}
