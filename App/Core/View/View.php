<?php

namespace App\Core\View;

use App\Core\Helpers\Error;
use App\Core\Helpers\Log;

final class View {

    // TODO: Add ability to customize layout class component
    /*
     * @var class-string<Component> $layout_class
     */
    // public static string $layout_class = LayoutComponent::class;

    /*
    * @param array<string, mixed> $data
    */
    public static function template(string $template_page, array $data = []): TemplateComponent {
        return new TemplateComponent($template_page, $data);
    }

    /*
    * @param JSScipt[] $scripts
    */
    public static function layout(Component $comp, string $title = 'Мой сайт', string $page_name = '', array $scripts = []): LayoutComponent {
        return new LayoutComponent($comp, title: $title, scripts: $scripts, page_name: $page_name);
    }

    /*
    * @param array<string, mixed> $data
    * @param JSScipt[] $scripts
    */
    public static function template_with_layout(
        string $template_page, array $data = [],
        string $title = 'Мой сайт', ?string $page_name = null, array $scripts = [],
    ): Component {
        Log::trace('View: template_with_layout');
        $comp = View::template($template_page, data: $data);
        return View::layout($comp, title: $title, page_name: $page_name ?? $template_page, scripts: $scripts);
    }

    public static function empty(): Component {
        return new ComponentFunc(fn() => Error::ok());
    }
}
