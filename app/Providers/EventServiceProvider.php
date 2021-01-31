<?php


namespace App\Providers;


use App\Core\ServiceProvider;
use App\Socket\Listeners\Chat\PublicChat\ChatListener;
use App\Socket\UserStorage;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //Remove public chat clients when they are offline
        event()->on('chat.public.removeUser', [ChatListener::class, 'removeUser']);

        //Remove private chat clients when they are offline
        event()->on('chat.private.user-left', [UserStorage::class, 'remove']);
    }
}