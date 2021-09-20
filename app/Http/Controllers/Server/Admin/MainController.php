<?php


namespace App\Http\Controllers\Server\Admin;


use App\Http\Controllers\Controller;
use React\Http\Message\Response;

class MainController extends Controller
{
    public function index(): Response
    {
        return $this->response->view('server/admin/index');
    }
}