<?php
namespace App\Controllers;

use App\Core\Helpers\Error;
use App\Core\Route\Request;
use App\Core\View\Component;
use App\Core\View\View;

final class Admin {
    public static function index(Request $req): Component {
        return View::template_with_layout('admin_home', title: 'Im in da house');
    }
}
