<?php

namespace App\Core\Model;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ARField {
    public function __construct(
        public string $column_name,
        public int $flags = 0,
    ) {}

    public const ID_FIELD = 0x1 << 0;
}
