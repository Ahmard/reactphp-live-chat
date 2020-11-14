<?php


namespace App\Providers;


use App\Core\ServiceProvider;
use App\Socket\UserStorage;
use App\Socket\Listeners\Chat\PublicChat\ChatListener;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //Remove public chat clients when they are offline
        event()->on('chat.public.removeUser', [ChatListener::class, 'removeUser']);

        //Remove private chat clients when they are offline
        event()->on('chat.private.user-left', [UserStorage::class, 'remove']);
    }
}