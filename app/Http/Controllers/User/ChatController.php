<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;

class ChatController extends Controller
{

    public function index()
    {
        return response()->view('user/chat/index');
    }

    public function privateChat()
    {
        return response()->view('user/chat/private', [
            'socket_prefix' => $_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX'],
        ]);
    }
}