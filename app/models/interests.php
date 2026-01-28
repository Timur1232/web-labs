<?php

namespace App\Models;

use App\Core\Helpers\ImgTag;

require_once 'app/core/helpers.php';

final class InterestsArticle {
    /** @param ImgTag[] $images */
    public function __construct(
        public string $title,
        public string $caption,
        public array $images,
    ) { }
}

/** @param ImgTag[] $images */
function article(
    string $title,
    string $caption,
    array $images
): InterestsArticle {
    return new InterestsArticle($title, $caption, $images);
}

final class InterestsSection {
    /** @param InterestsArticle[] $articles */
    public function __construct(
        public string $id,
        public string $title,
        public array $articles,
    ) { }
}

function section(
    string $id,
    string $title,
    array $articles,
): InterestsSection {
    return new InterestsSection($id, $title, $articles);
}

final class InterestsModel {
    /** @param InterestsSection[] $sections */
    public function __construct(
        public array $sections,
    ) { }
}
