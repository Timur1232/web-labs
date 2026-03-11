<?php
namespace App\Core\View;

use App\Core\Helpers\Result;

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

    public function render(): Result {
        if (!isset($this->template_page)) {
            return Result::ERROR("View: Cannot render tamplate - it is not set up. Use View::template(...) or set up \$template_page yourself before rendering.");
        }
        $page_file = self::$template_prefix.$this->template_page.'.php';
        if (!file_exists($page_file)) {
            return Result::ERROR("File {$page_file} not exist");
        }
        extract($this->data);
        include $page_file;
        return Result::OK();
    }
}
