<?php

namespace App\Views;

require_once 'app/core/view.php';

use App\Core\View;

final class CallbackView extends View {

    public static function default(): self {
        return new self();
    }

    public static function render_error(string $id, string $msg, bool $hx_swap = false): string {
        $swap = $hx_swap ? "hx-swap=\"{$id}\"" : '';
        return <<<HTML
            <span id="{$id}" class="error" {$swap}>{$msg}</span>
        HTML;
    }

}
