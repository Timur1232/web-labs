<?php

namespace App\Core\View;

use App\Core\Helpers\Error;
use Closure;

final class View {
    public const DEFAULT_TITLE = 'Мой сайт';
    /*
    * @param array<string, mixed> $data
    */
    public static function template(string $template_page, array $data = []): TemplateComponent {
        return new TemplateComponent($template_page, $data);
    }

    /*
    * @param JSScipt[] $scripts
    */
    public static function layout(Component $comp, string $title = self::DEFAULT_TITLE, string $page_name = '', array $scripts = []): LayoutComponent {
        // TODO: Add ability to customize layout class component
        return new LayoutComponent($comp, title: $title, scripts: $scripts, page_name: $page_name);
    }

    /*
    * @param array<string, mixed> $data
    * @param JSScipt[] $scripts
    */
    public static function template_with_layout(
        string $template_page, array $data = [],
        string $title = self::DEFAULT_TITLE, ?string $page_name = null, array $scripts = [],
    ): Component {
        $comp = View::template($template_page, data: $data);
        return View::layout($comp, title: $title, page_name: $page_name ?? $template_page, scripts: $scripts);
    }

    /*
    * @param array<string, mixed> $data
    * @param JSScipt[] $scripts
    */
    public static function htmx_template(
        bool $htmx,
        string $template_page, array $data = [],
        string $title = self::DEFAULT_TITLE, ?string $page_name = null, array $scripts = [],
    ): Component {
        if ($htmx) return self::template($template_page, data: $data);
        return self::template_with_layout($template_page, data: $data, title: $title, page_name: $page_name, scripts: $scripts);
    }

    public static function empty(): Component {
        return self::func(fn() => Error::OK());
    }
    /**
     * @param Closure(): Error $callback
     */
    public static function func(Closure $callback): Component {
        return new ComponentFunc($callback);
    }

    public static function string(string $str): Component {
        return self::func(function () use ($str) {
            echo $str;
            return Error::OK();
        });
    }

    public static function msg_tag(string $msg, string $id = 'msg'): Component {
        return self::func(function () use ($msg, $id) {
            echo <<<HTML
            <span id="{$id}">{$msg}</span>
            HTML;
            return Error::OK();
        });
    }

    public static function error_tag(string $err_msg, string $id = 'error'): Component {
        return self::msg_tag($err_msg, $id);
    }
}
