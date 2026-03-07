<?php
namespace App\Core\View;

use App\Core\Helpers\Error;
use App\Core\Helpers\Log;

final class TemplateComponent implements Component {

    public static string $template_prefix = 'App/Templates/';

    /*
    * @param array<string, mixed> $data
    */
    public function __construct(
        public string $template_page,
        public array $data = [],
    ) {}

    public function render(): Error {
        if (!isset($this->template_page)) {
            return Error::error("View: Cannot render tamplate - it is not set up. Use View::template(...) or set up \$template_page yourself before rendering.");
        }
        $page_file = self::$template_prefix.$this->template_page.'.php';
        if (!file_exists($page_file)) {
            return Error::error("File {$page_file} not exist");
        }
        extract($this->data);
        include $page_file;
        return Error::ok();
    }


}
