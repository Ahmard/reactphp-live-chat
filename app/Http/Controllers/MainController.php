<?php


namespace App\Http\Controllers;


use React\Http\Message\Response;

class MainController extends Controller
{
    public function index(): Response
    {
        if (request()->auth()->check()) {
            return view('index-logged');
        }

        return view('index');
    }

    public function chatIndex(): Response
    {
        return view('chat/index');
    }

    public function publicChat(): Response
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