<?php


namespace App\Http\Controllers;


use React\Http\Message\Response;

class MainController extends Controller
{
    public function index(): Response
    {
        if ($this->request->auth()->check()) {
            return $this->response->view('index-logged');
        }

        return $this->response->view('index');
    }

    public function chatIndex(): Response
    {
        return $this->response->view('chat/index');
    }

    public function publicChat(): Response
    {
        return $this->response->view('chat/chat', [
            'socket_prefix' => $_ENV['PUBLIC_CHAT_SOCKET_URL_PREFIX'],
            'room' => [
                'name' => 'reactphp-is-awesome',
                'user' => 'USER ' . clientCounter(),
            ],
        ]);
    }
}