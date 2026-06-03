<?php namespace App\Views;
use App\Core\View\Component_Func;
use App\Core\View\View;

final class Callback_View {
    /**
     * @param string[] $errors
     */
    public static function errors(array $errors): Component_Func {
        return View::func(function () use ($errors) {
            $str = '';
            foreach ($errors as $err) {
                $str .= self::error_tag($err);
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
