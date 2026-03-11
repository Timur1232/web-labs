<?php
namespace App\Views;

use App\Core\View\Component;
use App\Core\View\View;

final class StudyView {
    public static function index(): Component {
        return View::func(function () {
            return '';
        });
    }
}
