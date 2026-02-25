<?php

namespace App\Core\Helpers;

final class Helpers {
    public static function var_dump_str(mixed $val): string {
        ob_start();
        var_dump($val);
        return ob_get_clean();
    }

    public static function var_dump_preln(mixed $val): void {
        echo '<pre>';
        var_dump($val);
        echo '</pre><br>';
    }
}
