<?php


namespace App\Http\Controllers;


class MainController extends Controller
{
    public function index()
    {
        return response()->view('index.php', [
            'time' => time(),
            'test' => 'ReactPHP'
        ]);
    }

    public function chat()
    {
        return response()->view('chat.php', [
            'room' => [
                'name' => 'reactphp-is-awesome',
                'user' => 'user-'.clientCounter(),
            ]
        ]);
    }
}