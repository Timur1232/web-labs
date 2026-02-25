<?php

namespace App\Core\Model;

final class DependencyError {
    private function __construct(
        public string $rule,
        public string $reason,
    ) { }

    public static function new(string $rule, string $reason): self {
        return new self($rule, $reason);
    }
}
