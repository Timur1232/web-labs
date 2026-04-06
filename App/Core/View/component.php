<?php namespace App\Core\View;
use Closure;
use App\Core\Helpers\Error;

interface Component {
    function render(): string;
}

final class ComponentFunc implements Component {
    /**
     * @param Closure(): string $comp
     */
    public function __construct(
        public Closure $comp
    ) {}

    /**
     * @param Closure(): string $comp
     */
    public static function from(Closure $comp): self {
        return new self(comp: $comp);
    }

    public function render(): string {
        $comp_fn = $this->comp;
        return $comp_fn();
    }
}

final class TemplateComponent implements Component {

    public static string $template_prefix = 'App/Templates/';

    /*
    * @param array<string, mixed> $data
    */
    public function __construct(
        public string $template_page,
        public array $data = [],
    ) {}

    public function with(string $name, mixed $value): self {
        $this->data[$name] = $value;
        return $this;
    }

    public function render(): string {
        Error::assert(isset($this->template_page), "TemplateComponent: \$template_page must be set.");
        $page_file = self::$template_prefix.$this->template_page.'.php';
        Error::assert(file_exists($page_file), "TemplateComponent: File {$page_file} not exist");
        extract($this->data);
        ob_start();
        include $page_file;
        return ob_get_clean();
    }
}
