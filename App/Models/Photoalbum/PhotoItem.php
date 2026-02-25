<?php

namespace App\Models\Photoalbum;

final class PhotoItem {
    public function __construct(
        public string $filename,
        public string $alt,
        public string $title,
        public string $label,
    ) { }

    public static function from(string $filename, string $alt, string $title, string $label): self {
        return new self($filename, $alt, $title, $label);
    }
}
