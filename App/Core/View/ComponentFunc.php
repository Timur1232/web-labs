<?php
namespace App\Core\View;

use Closure;

final class ComponentFunc implements Component {
    /**
     * @param Closure(): void $comp
     */
    public function __construct(
        public Closure $comp
    ) {}

    /**
     * @param Closure(): void $comp
     */
    public static function from(Closure $comp): self {
        return new self(comp: $comp);
    }

    public function render(): void {
        $comp_fn = $this->comp;
        $comp_fn();
    }
}
