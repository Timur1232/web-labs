<?php namespace App\Models\Interests;

abstract class Tag {
    /** @param string[] $classes */
    protected function __construct(
        public string $id = '',
        public array $classes = [],
    ) { }

    public final function class(): string {
        $class = '';
        foreach ($this->classes as $c) {
            $class = $class . ' ' . $c;
        }
        return $class;
    }

    public final function add_class(string $class): self {
        $this->classes[] = $class;
        return $this;
    }

    public final function remove_class(string $class): self {
        $index = array_search($class, $this->classes);
        unset($this->classes[$index]);
        return $this;
    }
}

final class Img_Tag extends Tag {

    public function __construct(
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
    * @param string[] $classes
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

final class Interests_Article {
    /** @param Img_Tag[] $images */
    public function __construct(
        public string $title,
        public string $caption,
        public array $images,
    ) { }

    /** @param Img_Tag[] $images */
    public static function new(string $title, string $caption, array $images): self {
        return new self($title, $caption, $images);
    }
}

final class Interests_Section {
    /** @param Interests_Article[] $articles */
    public function __construct(
        public string $id,
        public string $title,
        public array $articles,
    ) { }

    /** @param Interests_Article[] $articles */
    public static function new(string $id, string $title, array $articles): self {
        return new self($id, $title, $articles);
    }
}

final class Interests_Model {
    /** @param Interests_Section[] $sections */
    public function __construct(
        public array $sections,
    ) { }

    /** @param Interests_Section[] $sections */
    public static function new(array $sections): self {
        return new self($sections);
    }

    public static function default(): self {
        return require 'App/Models/Instances/Interests.php';
    }
}
