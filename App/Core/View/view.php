<?php namespace App\Core\View;
use Closure;

enum JsScriptType : string {
    case Text   = 'text/javascript';
    case Module = 'module';
}

final class JsScript {
    public function __construct(
        public string $src = '',
        public JsScriptType $type = JsScriptType::Text,
        public bool $defer = false,
    ) { }

    public static function from(string $src, JsScriptType $type = JsScriptType::Text, bool $defer = false): self {
        return new self($src, $type, $defer);
    }

    public function render_script(): string {
        $defer = $this->defer ? 'defer' : '';
        return "<script src=\"{$this->src}\" type=\"{$this->type->value}\" {$defer}></script>";
    }
}

final class View {
    public const DEFAULT_TITLE = 'Мой сайт';
    /*
    * @param array<string, mixed> $data
    */
    public static function template(string $template_page, array $data = []): TemplateComponent {
        return new TemplateComponent($template_page, $data);
    }

    public static function empty(): ComponentFunc {
        return self::func(fn() => '');
    }
    /**
     * @param Closure(): string $callback
     */
    public static function func(Closure $callback): ComponentFunc {
        return new ComponentFunc($callback);
    }

    public static function string(string $str): ComponentFunc {
        return self::func(fn() => $str);
    }

    public static function msg_tag(string $msg, string $id = 'msg'): ComponentFunc {
        return self::func(function () use ($msg, $id) {
            return <<<HTML
            <span id="{$id}">{$msg}</span>
            HTML;
        });
    }

    public static function error_tag(string $err_msg, string $id = 'error'): ComponentFunc {
        return self::msg_tag($err_msg, $id);
    }
}
