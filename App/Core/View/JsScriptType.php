<?php

namespace App\Core\View;

enum JsScriptType : string {
    case Text   = 'text/javascript';
    case Module = 'module';
}

