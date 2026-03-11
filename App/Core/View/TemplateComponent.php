<?php
namespace App\Core\View;

use App\Core\Helpers\Error;

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

    public function render(): void {
        Error::assert(isset($this->template_page), "TemplateComponent: \$template_page must be set.");
        $page_file = self::$template_prefix.$this->template_page.'.php';
        Error::assert(file_exists($page_file), "TemplateComponent: File {$page_file} not exist");
        extract($this->data);
        include $page_file;
    }
}
