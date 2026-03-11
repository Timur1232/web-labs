<?php
namespace App\Views;

use App\Core\Helpers\Result;
use App\Core\View\Component;
use App\Core\View\View;

final class StudyView {
    public static function index(): Component {
        return View::func(function () {
            return Result::OK();
        });
    }
}
