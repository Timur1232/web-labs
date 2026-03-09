<?php
namespace App\Controllers;

use App\Core\Helpers\Error;
use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;

final class Admin {
    public const TITLE = 'Im in da house';
    public static function index(Request $req): Component {
        return View::template_with_layout('admin_home', title: self::TITLE);
    }

    public static function load_guest_book(Request $req): Component {
        return View::template_with_layout('load_guest_book', title: self::TITLE);
    }
}
