<?php

namespace App\Models\Interests;

final class InterestsArticle {
    /** @param ImgTag[] $images */
    public function __construct(
        public string $title,
        public string $caption,
        public array $images,
    ) { }

    /** @param ImgTag[] $images */
    public static function new(string $title, string $caption, array $images): self {
        return new self($title, $caption, $images);
    }
}
