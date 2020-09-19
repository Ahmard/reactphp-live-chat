<?php


namespace App\Http\Controllers\Server\Admin;


use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function index()
    {
        return view('server/admin/index');
    }
}