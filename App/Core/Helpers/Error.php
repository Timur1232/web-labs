<?php

namespace App\Core\Helpers;

use App\Core\Helpers\Log;
use App\Core\View\View;

/*
 * @template T
 * @template E
 */
final class Error {
    /**
     * @param T|null $val
     * @param E|null $err_val
     */
    public function __construct(
        public $val = null,
        public bool $ok = true,
        public $err_val = null
    ) {}

    /**
     * @param T|null $val
     */
    public static function OK($val = null): self {
        return new self(ok: true, err_val: null, val: $val);
    }

    /**
     * @param E|null $error_value
     */
    public static function ERROR($error_value): self {
        return new self(ok: false, err_val: $error_value, val: null);
    }

    public function log(string $prefix = ''): void {
        Log::error("{$prefix}: ".strval($this->err_val));
    }

    public static function TODO(string $msg): self {
        return new self(ok: false, err_val: "[NOT IMPLEMENTED]: {$msg}");
    }

    public static function send_error_msg_and_die(int $code, string $msg): void {
        // Log::trace("Error: $msg");
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
}
