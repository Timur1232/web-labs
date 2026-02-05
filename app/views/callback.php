<?php

namespace App\Views;

require_once 'app/core/view.php';

use App\Core\View;

final class CallbackView extends View {

    private function __construct() {
        parent::__construct();
    }

    public static function default(): self {
        return new self();
    }

    public static function error_tag(string $msg): string {
        return <<<HTML
            <span class="error">{$msg}<br></span>
        HTML;
    }

}
