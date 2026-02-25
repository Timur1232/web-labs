<?php

namespace App\Core\View;

final class JsScript {
    public function __construct(
        public string $src,
        public JsScriptType $type,
    ) { }

    public static function from(string $src, JsScriptType $type = JsScriptType::Text): self {
        return new self($src, $type);
    }

    public function render_script(): string {
        return "<script src=\"{$this->src}\" type=\"{$this->type->value}\"></script>";
    }
}
