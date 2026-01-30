<?php

namespace App\Models;

require_once 'app/core/helpers.php';

use App\Core\Helpers\ImgTag;

final class InterestsArticle {
    /** @param ImgTag[] $images */
    private function __construct(
        public string $title,
        public string $caption,
        public array $images,
    ) { }

    /** @param ImgTag[] $images */
    public static function new(string $title, string $caption, array $images): self {
        return new self($title, $caption, $images);
    }
}

/** @param ImgTag[] $images */
function article(
    string $title,
    string $caption,
    array $images
): InterestsArticle {
    return InterestsArticle::new($title, $caption, $images);
}

final class InterestsSection {
    /** @param InterestsArticle[] $articles */
    private function __construct(
        public string $id,
        public string $title,
        public array $articles,
    ) { }

    /** @param InterestsArticle[] $articles */
    public static function new(string $id, string $title, array $articles): self {
        return new self($id, $title, $articles);
    }
}

function section(
    string $id,
    string $title,
    array $articles,
): InterestsSection {
    return InterestsSection::new($id, $title, $articles);
}

final class InterestsModel {
    /** @param InterestsSection[] $sections */
    private function __construct(
        public array $sections,
    ) { }

    /** @param InterestsSection[] $sections */
    public static function new(array $sections): self {
        return new self($sections);
    }

    public static function default(): self {
        return require 'app/models/instances/interests.php';
    }
}
