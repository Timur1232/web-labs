<?php

namespace App\Core\View;

use App\Core\Helpers\Error;

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

    public static function empty(): Component {
        return new ComponentFunc(fn() => Error::OK());
    }
}
