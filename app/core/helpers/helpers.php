<?php

namespace App\Core\Helpers;

if (!defined('STDIN')) define('STDIN', fopen('php://stdin', 'rb'));
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

function var_dump_str(mixed $val): string {
    ob_start();
    var_dump($val);
    return ob_get_clean();
}

function var_dump_preln(mixed $val): void {
    echo '<pre>';
    var_dump($val);
    echo '</pre><br>';
}

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
    }

    public final function remove_class(string $class): self {
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
