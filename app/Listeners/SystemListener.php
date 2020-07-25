<?php
namespace App\Listeners;

use App\Listeners\Listener;
use App\Socket\State;

class SystemListener extends Listener
{
    public function ping()
    {
        State::handlePing($this->client);
        
        resp($this->client)->send('message', 'Alhamdulillah');
    }
    
    public function pong()
    {
        
    }
}