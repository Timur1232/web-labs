<?php

namespace App\Core\Helpers;

use App\Core\Helpers\Log;
use App\Core\View\View;

final class Error {
    public static function send_error_msg_and_die(int $code, string $msg): void {
        while (ob_end_clean());
        http_response_code($code);
        echo $msg;
        die();
    }

    public static function not_found(string $path): void {
        $err = View::default()
            ->data('title', '404 Not Found')
            ->data('msg', "{$path} не найден.");
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(404, $err->render_layout(template_page: 'error', title: 'Упс'));
    }

    public static function method_not_allowed(): void {
        $err = View::default()
            ->data('title', '405 Method Not Allowed');
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(405, $err->render_layout(template_page: 'error', title: 'Упс'));
    }

    public static function internal_error(): void {
        $err = View::default()
            ->data('title', '500 Internal Server Error');
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(500, $err->render_layout(template_page: 'error', title: 'Упс'));
    }

    public static function assert(bool $cond, string $msg): void {
        if (!$cond) {
            Log::assert($msg);
            self::internal_error();
        }
    }

    public static function todo(string $msg): void {
        Log::println_err('[TODO]: '.$msg);
    }
}
