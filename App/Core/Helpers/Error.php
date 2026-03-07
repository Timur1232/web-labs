<?php

namespace App\Core\Helpers;

use App\Core\Helpers\Log;
use App\Core\View\View;

final class Error {
    public function __construct(
        public bool $ok = true,
        public ?string $error = null
    ) {}

    public static function ok(): self {
        return new self(ok: true, error: null);
    }

    public static function error(string $error_msg): self {
        return new self(ok: false, error: $error_msg);
    }

    public static function send_error_msg_and_die(int $code, string $msg): void {
        while (ob_end_clean());
        http_response_code($code);
        echo $msg;
        die();
    }

    public static function not_found(string $path): void {
        $err = View::layout(View::template('error', data: [
            'title' => '404 Not Found',
            'msg'   => "{$path} не найден.",
        ]), title: 'Упс');
        ob_start();
        $err->render();
        $msg = ob_get_clean();
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(404, $msg);
    }

    public static function method_not_allowed(): void {
        $err = View::layout(View::template('error', data: [
            'title' => '405 Method Not Allowed',
        ]), title: 'Упс');
        ob_start();
        $err->render();
        $msg = ob_get_clean();
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(405, $msg);
    }

    public static function internal_error(): void {
        $err = View::layout(View::template('error', data: [
            'title' => '500 Internal Server Error',
        ]), title: 'Упс');
        ob_start();
        $err->render();
        $msg = ob_get_clean();
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(500, $msg);
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
