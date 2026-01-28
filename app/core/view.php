<?php

namespace App\Core;

require_once 'app/core/layout.php';

enum JSScriptType : string {
    case Text   = 'text/javascript';
    case Module = 'module';
}

final class JSScript {
    public function __construct(
        public string $src,
        public JSScriptType $type,
    ) { }

    public function render_script(): string {
        return "<script src=\"{$this->src}\" type=\"{$this->type->value}\"></script>";
    }
}

class View {

    /** @var array<JSScript> $scripts */
    public array $scripts = [];
    public array $data = [];

    public final function render_layout(string $template_page, string $page_name = null, string $title = null): string {
        ob_start();
        layout(new LayoutData(
            $title ?? 'Мой сайт',
            $page_name ?? $template_page,
            $template_page,
            $this,
        ));
        return ob_get_clean();
    }

    public final function render(string $template_page): string {
        $page_file = 'app/templates/'.$template_page.'.php';
        if (!file_exists($page_file)) {
            http_response_code(500);
            echo '<h1>500 Internal Server Error</h1>';
            echo '<a href="/">Home</a>';
            die();
        }
        $view = $this;
        extract($this->data);
        ob_start();
        include $page_file;
        return ob_get_clean();
    }

    public final function render_hx(Request $req, string $template_page, string $page_name = null, string $title = null): string {
        return $req->htmx
            ? $this->render(template_page: $template_page)
            : $this->render_layout(template_page: $template_page, page_name: $page_name, title: $title);
    }

    public final function script(string $src, JSScriptType $type = JSScriptType::Text): self {
        $this->scripts[] = new JSScript($src, $type);
        return $this;
    }

    public final function data(string $key, mixed $val): self {
        $this->data[$key] = $val;
        return $this;
    }
}
