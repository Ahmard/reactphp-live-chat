<?php


namespace App\Http\Controllers\Server\Admin;


use App\Http\Controllers\Controller;
use React\Http\Message\Response;

class MainController extends Controller
{
    public function index(): Response
    {
        return view('server/admin/index');
    }
}