<?php
namespace App\Core\Test;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Test {
    public function __construct(
        public string $test_name = 'no name',
    ) {}
}
