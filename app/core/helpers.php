<?php

namespace App\Core\Helpers;

require_once 'app/core/view.php';

use App\Core\View;

if (!defined('STDIN')) define('STDIN', fopen('php://stdin', 'rb'));
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

function var_dump_str(mixed $val): string {
    ob_start();
    var_dump($val);
    return ob_get_clean();
}

function var_dump_preln(mixed $val): void {
    echo '<pre>';
    var_dump($val);
    echo '</pre><br>';
}

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

final class Log {
    public static function printf(string $fmt, mixed ...$args): void {
        fprintf(STDOUT, $fmt, ...$args);
    }

    public static function printfln(string $fmt, mixed ...$args): void {
        fprintf(STDOUT, "$fmt\n", ...$args);
    }

    public static function println_err(string $msg): void {
        fputs(STDERR, "\e[91m{$msg}\e[0m\n");
    }

    public static function trace(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(STDOUT, "\e[37m [TRACE] {$fl}{$msg}\e[0m\n");
    }

    public static function info(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(STDOUT, "\e[32m [INFO] {$fl}{$msg}\e[0m\n");
    }

    public static function warning(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(STDOUT, "\e[33m [WARNING] {$fl}{$msg}\e[0m\n");
    }

    public static function error(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(STDERR, "\e[91m [ERROR] {$fl}{$msg}\e[0m\n");
    }

    public static function assert(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(STDERR, "\e[31m [ASSERT] {$fl}{$msg}\e[0m\n");
    }

    public static function log(string $msg, ?string $file = null, ?int $line = null): void {
        $line ??= 0;
        $fl = isset($file) ? "{$file}:{$line}: " : '';
        fprintf(STDOUT, " [LOG] {$fl}{$msg}\n");
    }
}

abstract class Tag {

    /** @param string[] $classes */
    protected function __construct(
        public string $id = '',
        public array $classes = [],
    ) { }

    public final function class(): string {
        $class = '';
        foreach ($this->classes as $c) {
            $class = $class . ' ' . $c;
        }
        return $class;
    }

    public final function add_class(string $class): self {
        $this->classes[] = $class;
    }

    public final function remove_class(string $class): self {
        $index = array_search($class, $this->classes);
        unset($this->classes[$index]);
        return $this;
    }
}

final class ImgTag extends Tag {

    private function __construct(
        public string $src,
        public string $alt,
        public string $title,
        string $id = '',
        /** @param stirng[] $classes */
        array $classes = [],
    ) {
        parent::__construct($id, $classes);
    }

    /**
    * @param stirng[] $classes
    */
    public static function new(
        string $src = '', string $alt = '', string $title = '',
        string $id = '', array $classes = []
    ): self {
        return new self($src, $alt, $title, $id, $classes);
    }

    public function render(): string {
        $id = $this->id ? "id=\"{$this->id}\"" : '';
        $class = $this->class();
        $class = $class ? "class=\"{$class}\"" : '';
        return <<<HTML
            <img {$id} {$class} src="{$this->src}" alt="{$this->alt}" title="{$this->id}" />
        HTML;
    }
}

/**
* @param stirng[] $classes
*/
function img(
    string $src = '', string $alt = '', string $title = '',
    string $id = '', array $classes = []): ImgTag
{
    return ImgTag::new($src, $alt, $title, $id, $classes);
}
