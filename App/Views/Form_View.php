<?php namespace App\Views;

use App\Core\View\Component_Func;
use App\Core\View\Component;
use App\Core\View\View;

final class Form_View {
    /**
     * @param array<string,string> $attrs
     */
    public static function input(string $type, string $id, ?string $label = null, array $attrs = []): Component_Func {
        return View::func(function() use ($type, $id, $label, $attrs) {
            $label = isset($label) ? <<<HTML
                <label for="{$id}">$label</label>
                HTML
                : '';
            $attrs = implode(' ', array_map(fn($k, $v) => "{$k}=\"{$v}\"", array_keys($attrs), $attrs));
            return <<<HTML
                {$label}
                <input type="{$type}" class="input-text" id="{$id}" name="{$id}" {$attrs}></input>
            HTML;
        });
    }

    /**
     * @param array<string,string> $attrs
     */
    public static function textarea(string $id, int $row = 1, ?string $label = null, array $attrs = []): Component_Func {
        return View::func(function() use ($id, $row, $label, $attrs) {
            $label = isset($label) ? <<<HTML
                <label for="{$id}">$label</label>
                HTML
                : '';
            $attrs = implode(' ', array_map(fn($k, $v) => "{$k}=\"{$v}\"", array_keys($attrs), $attrs));
            return <<<HTML
                <textarea id="{$id}" name="{$id}" row="{$row}" {$attrs}></textarea>
                HTML;
        });
    }

    /**
     * @param Component[] $comps
     */
    public static function form(string $action, string $method, array $comps, string $enctype = 'text/plain'): Component_Func {
        return View::func(function() use ($action, $method, $comps, $enctype) {
            $form = <<<HTML
                <form class="callback-form" action="{$action}" method="{$method}" enctype="$enctype">
                HTML;
            foreach($comps as $c) {
                $form .= $c->render();
            }
            return $form . '</form>';
        });
    }
}
