<?php
namespace App\Core;

use App\Core\Helpers\Log;
use Exception;

final class Init {
    // Path to corresponding directory of currently loaded module
    public static string $current_module_path;

    // Name of the funciton inside `$current_module_path` directory, that should load module
    // It always has trailing '/' on end
    public static string $module_file_name = 'init';

    // Helper function to load files of the module
    public static function load(string $file_name): void {
        require_once self::$current_module_path.$file_name.'.php';
    }

    // Autoload callback to pass into `spl_autoload_register` function
    public static function autoload(string $class_name): void {
        require_once './App/Core/Helpers/Log.php';
        $file_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
        Log::trace("trying exact match: {$file_path}");
        if (file_exists($file_path)) {
            Log::info("exact match: {$file_path}");
            require_once $file_path;
            return;
        }

        $splited = explode('\\', $class_name);
        $module_path = implode(DIRECTORY_SEPARATOR, array_slice($splited, 0, count($splited)-1));
        Log::trace("trying file module: {$module_path}");
        if (file_exists($module_path.'.php')) {
            Log::info("file module: {$module_path}");
            require_once $module_path.'.php';
            return;
        }

        $init_path = $module_path.DIRECTORY_SEPARATOR.self::$module_file_name.'.php';
        Log::trace("trying init file: {$init_path}");
        if (!file_exists($init_path)) {
            throw new Exception("Init: Unable to find {$class_name} class :/");
        }
        self::$current_module_path = $module_path.DIRECTORY_SEPARATOR;
        Log::info("init file: {$init_path}");
        require_once $init_path;
    }
}

