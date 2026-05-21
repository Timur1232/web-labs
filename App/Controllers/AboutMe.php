<?php namespace App\Controllers;
use App\Core\Context\Request;
use App\Core\Context\Response;
use App\Views\Common_View;

final class AboutMe {
    public static function index(Request $req): Response {
        $comp = Common_View::template_with_layout(template_page: 'about_me', title: 'Обо мне', user: $req->additional['user']);
        return Response::view($comp);
    }
}
