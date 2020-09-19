<?php


namespace App\Http\Controllers;


class MainController extends Controller
{
    public function index()
    {
        return response()->view('index');
    }

    public function chatIndex()
    {
        return response()->view('chat/index');
    }

    public function publicChat()
    {
        return response()->view('chat/chat', [
            'socket_prefix' => $_ENV['PUBLIC_CHAT_SOCKET_URL_PREFIX'],
            'room' => [
                'name' => 'reactphp-is-awesome',
                'user' => 'USER ' . clientCounter(),
            ],
        ]);
    }
}