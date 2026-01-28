<?php

namespace App\Core;

require_once 'app/core/layout.php';

enum JSScriptType : string {
    case Text   = 'text/javascript';
    case Module = 'module';
}

final class JSScript {
    public string $src;
    public JSScriptType $type;

    public function __construct(string $src, JSScriptType $type = JSScriptType::Text) {
        $this->src = $src;
        $this->type = $type;
    }

    public function get_script(): string {
        return "<script src=\"{$this->src}\" type=\"{$this->type->value}\"></script>";
    }
}

final class View {

    public string $page = 'index';
    /** @var ?array<JSScript> $scripts */
    public array $scripts = [];
    /** @var array<string, mixed> $data */
    public array $data;

    public function __construct(string $page, string $title = null) {
        if ($page != null)  $this->page = $page;
        $this->data('title', $title ?? 'Мой сайт');
    }

    public function render_layout(): void {
        layout($this);
    }

    public function render(): void {
        $page_file = 'app/views/'.$this->page.'.php';
        if (!file_exists($page_file)) {
            http_response_code(500);
            echo '<h1>500 Internal Server Error</h1>';
            echo '<a href="/">Home</a>';
            die();
        }
        extract($this->data);
        include $page_file;
    }

    public function data(string $key, mixed $val): self {
        $this->data[$key] = $val;
        return $this;
    }

    public function script(string $src, JSScriptType $type = JSScriptType::Text): self {
        $this->scripts[] = new JSScript($src, $type);
        return $this;
    }

}
