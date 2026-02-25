<?php

namespace App\Models\Interests;

final class InterestsModel {
    /** @param InterestsSection[] $sections */
    public function __construct(
        public array $sections,
    ) { }

    /** @param InterestsSection[] $sections */
    public static function new(array $sections): self {
        return new self($sections);
    }

    public static function default(): self {
        return require 'App/Models/Instances/Interests.php';
    }
}
