<?php


namespace App\Http\Controllers;


class MainController extends Controller
{
    public function index()
    {
        if (request()->auth()->check()) {
            return view('index-logged');
        }

        return view('index');
    }

    public function chatIndex()
    {
        return view('chat/index');
    }

    public function publicChat()
    {
        return view('chat/chat', [
            'socket_prefix' => $_ENV['PUBLIC_CHAT_SOCKET_URL_PREFIX'],
            'room' => [
                'name' => 'reactphp-is-awesome',
                'user' => 'USER ' . clientCounter(),
            ],
        ]);
    }
}