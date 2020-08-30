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
        $socketUrl = "ws://{$_ENV['HOST']}:{$_ENV['PORT']}{$_ENV['CHAT_SOCKET_URL_PREFIX']}";
        return response()->view('chat.php', [
            'socket_url' => $socketUrl,
            'room' => [
                'name' => 'reactphp-is-awesome',
                'user' => 'user-'.clientCounter(),
            ],
        ]);
    }
}