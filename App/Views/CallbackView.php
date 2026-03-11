<?php

namespace App\Views;

use App\Core\View\Component;
use App\Core\View\View;

final class CallbackView {
    /**
     * @param string[] $errors
     */
    public static function errors(array $errors): Component {
        return View::func(function () use ($errors) {
            $str = '';
            foreach ($errors as $err) {
                $str .= CallbackView::error_tag($err);
            }
            return $str;
        });
    }

    public static function error_tag(string $msg): string {
        return <<<HTML
            <span class="error">{$msg}<br></span>
        HTML;
    }
}
