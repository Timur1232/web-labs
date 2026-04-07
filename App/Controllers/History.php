<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Core\View\Component;
use App\Views\CommonView;

final class History {
    public static function index(Request $req): Response {
        $comp = CommonView::template_with_layout('history', title: 'История просмотра');
        return Response::view($comp);
    }
}
