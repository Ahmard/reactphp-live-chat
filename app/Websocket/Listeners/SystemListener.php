<?php

namespace App\Websocket\Listeners;

use Server\Websocket\State;

class SystemListener extends Listener
{
    public function ping(): void
    {
        State::handlePing($this->client);

        resp($this->client)->send('message', 'Ping received.');
    }

    public function pong(): void
    {
        event()->emit('system.pong', [$this->client]);
    }
}