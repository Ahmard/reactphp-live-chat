<?php


namespace App\Providers;


use App\Core\ServiceProvider;
use App\Models\Client;
use App\Socket\Listeners\Chat\PublicChat\ChatListener;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //Remove public chat clients when they are offline
        event()->on('chat.public.removeUser', [ChatListener::class, 'removeUser']);

        //Remove private chat clients when they are offline
        event()->on('chat.private.user-left', [Client::class, 'remove']);
    }
}