<?php

namespace App\Core\Helpers;

use App\Core\Helpers\Log;
use App\Core\View\View;
use App\Views\CommonView;

final class Error {
    public static function send_error_msg_and_die(int $code, string $msg): void {
        while (ob_end_clean());
        http_response_code($code);
        echo $msg;
        die();
    }

    public static function not_found(string $path): void {
        $err = CommonView::layout(View::template('error', data: [
            'title' => '404 Not Found',
            'msg'   => "{$path} не найден.",
        ]), title: 'Упс', page_name: 'error');
        ob_start();
        $err->render();
        $msg = ob_get_clean();
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(404, $msg);
    }

    public static function method_not_allowed(): void {
        $err = CommonView::layout(View::template('error', data: [
            'title' => '405 Method Not Allowed',
        ]), title: 'Упс', page_name: 'error');
        ob_start();
        $err->render();
        $msg = ob_get_clean();
        header('HX-Retarget: body');
        header('HX-Reswap: innerHTML');
        self::send_error_msg_and_die(405, $msg);
    }

    public static function internal_error(): void {
        $err = CommonView::layout(View::template('error', data: [
            'title' => '500 Internal Server Error',
        ]), title: 'Упс', page_name: 'error');
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
