<?php

namespace App\Views;

final class CallbackView {
    public static function error_tag(string $msg): string {
        return <<<HTML
            <span class="error">{$msg}<br></span>
        HTML;
    }
}
