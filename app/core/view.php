<?php

require_once 'app/core/layout.php';

enum JSScriptType : string {
    case Text = 'text/javascript';
    case Module = 'module';
}

final class JSScript {
    public string $src;
    public JSScriptType $type;

    public function __construct(string $src, JSScriptType $type = JSScriptType::Text) {
        $this->src = $src;
        $this->type = $type;
    }
}

final class View {

    /**
    * @var ?array<JSScript> $scripts
    */
    public ?array $scripts = null;
    public string $title = 'Мой сайт';
    public string $page = 'index';

    public function __construct(string $page, string $title = null) {
        if ($page != null)  $this->page = $page;
        if ($title != null) $this->title = $title;
    }

    public function render(): void {
        call_user_func('layout', $this);
    }

}
