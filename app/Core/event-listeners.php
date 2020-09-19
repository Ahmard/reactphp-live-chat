<?php

use App\Socket\Listeners\Chat\PublicChat\ChatListener;

event()->on('chat.public.removeUser', fn($client) => ChatListener::removeUser($client));