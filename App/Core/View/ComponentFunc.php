<?php
namespace App\Core\View;

use Closure;

final class ComponentFunc implements Component {
    /**
     * @param Closure(): string $comp
     */
    public function __construct(
        public Closure $comp
    ) {}

    /**
     * @param Closure(): string $comp
     */
    public static function from(Closure $comp): self {
        return new self(comp: $comp);
    }

    public function render(): string {
        $comp_fn = $this->comp;
        $comp_fn();
    }
}
