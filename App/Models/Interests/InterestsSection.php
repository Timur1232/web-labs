<?php

namespace App\Models\Interests;

final class InterestsSection {
    /** @param InterestsArticle[] $articles */
    public function __construct(
        public string $id,
        public string $title,
        public array $articles,
    ) { }

    /** @param InterestsArticle[] $articles */
    public static function new(string $id, string $title, array $articles): self {
        return new self($id, $title, $articles);
    }
}
