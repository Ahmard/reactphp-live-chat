<?php


namespace App\Socket\Listeners;


class MainListener extends Listener
{
    public function hello()
    {
        $message = $this->request->payload()->message ?? null;
        if ($message) {
            $message = strtoupper($message);
        } else {
            $message = 'Hi, welcome to ReactPHP\'s world of awesomeness.';
        }

        resp($this->client)->send('hail.reactphp', $message);
    }
}