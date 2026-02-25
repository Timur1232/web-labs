<?php

namespace App\Core\View;

use App\Core\Helpers\Error;
use App\Core\Route\Request;

final class View {

    /*
    * @param JSScipt[] $scripts
    * @param array<string, mixed> $data
    */
    public function __construct(
        public array $scripts = [],
        public array $data = [],
    ) { }

    public static function default(): self {
        return new self();
    }

    /*
    * @param JSScipt[] $scripts
    * @param array<string, mixed> $data
    */
    public static function new(array $scripts, array $data): self {
        return new self($scripts, $data);
    }

    public final function render_layout(string $template_page, string $page_name = null, string $title = null): string {
        require_once 'App/Core/View/Layout.php';
        ob_start();
        layout(LayoutData::new(
            $title ?? 'Мой сайт',
            $page_name ?? $template_page,
            $template_page,
            $this,
        ));
        return ob_get_clean();
    }

    public final function render(string $template_page): string {
        $page_file = 'App/Templates/'.$template_page.'.php';
        if (!file_exists($page_file)) {
            Error::internal_error();
        }
        extract($this->data);
        ob_start();
        include $page_file;
        return ob_get_clean();
    }

    public final function render_hx(
        Request $req, string $template_page, string $page_name = null, string $title = null): string {
        return $req->htmx
            ? $this->render(template_page: $template_page)
            : $this->render_layout(template_page: $template_page, page_name: $page_name, title: $title);
    }

    public final function script(string $src, JsScriptType $type = JsScriptType::Text): self {
        $this->scripts[] = JsScript::from($src, $type);
        return $this;
    }

    public final function data(string $key, mixed $val): self {
        $this->data[$key] = $val;
        return $this;
    }
}
