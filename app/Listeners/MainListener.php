<?php


namespace App\Listeners;


class MainListener extends Listener
{
    public function hello()
    {
        resp($this->client)->send('hail.reactphp', strtoupper($this->request->message->message));
    }
}