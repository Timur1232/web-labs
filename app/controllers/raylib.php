<?php

namespace App\Controllers;
require_once './app/core/request.php';
require_once './app/core/helpers.php';

use App\Core\Helpers\Error;
use FFI;
use App\Core\Request;

final class Raylib {
    public static function raylib(Request $r): void {
        $ffi = FFI::load("./raylib_php.h");
        Error::assert(isset($ffi), "Cannot load raylib.so");
        $ffi->InitWindow(800, 600, "Hello from php");
        $msg = '';
        $cur_char = 0;
        $cool_down = 100;
        $time = 0;

        $bg_color = $ffi->new("Color");
        $bg_color->r = 0x18;
        $bg_color->g = 0x18;
        $bg_color->b = 0x18;
        $bg_color->a = 0xff;

        $rec_color = $ffi->new("Color");
        $rec_color->r = 0xff;
        $rec_color->g = 0x00;
        $rec_color->b = 0x00;
        $rec_color->a = 0xff;
        $x = 100;
        $y = 100;

        $text_color = $ffi->new("Color");
        $text_color->r = 0xff;
        $text_color->g = 0xff;
        $text_color->b = 0xff;
        $text_color->a = 0xff;

        $ffi->SetTargetFPS(30);
        while (!$ffi->WindowShouldClose()) {
            $ffi->BeginDrawing();
            $ffi->ClearBackground($bg_color);
            $char = $ffi->GetCharPressed();
            if ($char !== 0 && ($time >= $cool_down || $char != $cur_char)) {
                $msg .= chr($char);
                $cur_char = $char;
                $time = 0;
            }

            if ($ffi->IsKeyPressed(257)) { // Enter
                break;
            } else if ($ffi->IsKeyPressed(259)) { // Backspace
                $msg = substr($msg, 0, -1);
            }

            $time += 32;
            $ffi->DrawText($msg, 100, 50, 40, $text_color);
            $ffi->DrawRectangle($x, $y, 100, 100, $rec_color);
            $x += 1; $y += 1;
            $ffi->EndDrawing();
        }
        $ffi->CloseWindow();
        echo $msg;
    }
}
