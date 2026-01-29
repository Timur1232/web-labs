<?php

namespace App\Core\Helpers;

abstract class Tag {

    /** @param string[] $classes */
    protected function __construct(
        public string $id = '',
        public array $classes = [],
    ) { }

    public function class(): string {
        $class = '';
        foreach ($this->classes as $c) {
            $class = $class . ' ' . $c;
        }
        return $class;
    }

    public function add_class(string $class): self {
        $this->classes[] = $class;
    }

    public function remove_class(string $class): self {
        $index = array_search($class, $this->classes);
        unset($this->classes[$index]);
        return $this;
    }
}

final class ImgTag extends Tag {

    private function __construct(
        public string $src,
        public string $alt,
        public string $title,
        string $id = '',
        /** @param stirng[] $classes */
        array $classes = [],
    ) {
        parent::__construct($id, $classes);
    }

    /**
    * @param stirng[] $classes
    */
    public static function new(
        string $src = '', string $alt = '', string $title = '',
        string $id = '', array $classes = []
    ): self {
        return new self($src, $alt, $title, $id, $classes);
    }

    public function render(): string {
        $id = $this->id ? "id=\"{$this->id}\"" : '';
        $class = $this->class();
        $class = $class ? "class=\"{$class}\"" : '';
        return <<<HTML
            <img {$id} {$class} src="{$this->src}" alt="{$this->alt}" title="{$this->id}" />
        HTML;
    }
}

/**
* @param stirng[] $classes
*/
function img(
    string $src = '', string $alt = '', string $title = '',
    string $id = '', array $classes = []): ImgTag
{
    return ImgTag::new($src, $alt, $title, $id, $classes);
}
