<?php
namespace App\Socket\Listeners;

use App\Socket\Listeners\Listener;
use App\Socket\State;

class SystemListener extends Listener
{
    public function ping()
    {
        State::handlePing($this->client);
        
        resp($this->client)->send('message', 'Ping received.');
    }
    
    public function pong()
    {
        
    }
}