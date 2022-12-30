<?php


namespace App\Providers;


use App\Websocket\Listeners\Chat\PublicChat\ChatListener;
use App\Websocket\Model;
use Server\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //Remove public chat clients when they are offline
        event()->on('chat.public.removeUser', [ChatListener::class, 'removeUser']);

        //Remove private chat clients when they are offline
//        event()->on('chat.private.user-left', [Model::class, 'remove']);
    }
}