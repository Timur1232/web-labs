<?php

namespace App\Core\View;

use App\Core\Helpers\Result;
use Closure;

final class View {
    public const DEFAULT_TITLE = 'Мой сайт';
    /*
    * @param array<string, mixed> $data
    */
    public static function template(string $template_page, array $data = []): TemplateComponent {
        return new TemplateComponent($template_page, $data);
    }

    public static function empty(): Component {
        return self::func(fn() => '');
    }
    /**
     * @param Closure(): Result $callback
     */
    public static function func(Closure $callback): Component {
        return new ComponentFunc($callback);
    }

    public static function string(string $str): Component {
        return self::func(fn() => $str);
    }

    public static function msg_tag(string $msg, string $id = 'msg'): Component {
        return self::func(function () use ($msg, $id) {
            return <<<HTML
            <span id="{$id}">{$msg}</span>
            HTML;
        });
    }

    public static function error_tag(string $err_msg, string $id = 'error'): Component {
        return self::msg_tag($err_msg, $id);
    }
}
