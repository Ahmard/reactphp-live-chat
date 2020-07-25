<?php
use App\Core\Colis\Colis;

Colis::prefix('system.')
    ->group(function($colis){
        $colis->listen('ping', 'SystemListener@ping');
        $colis->listen('pong', 'SystemListener@pong');
    });


Colis::listen('hail.reactphp', 'MainListener@hello');

Colis::prefix('chat.')
    ->namespace('Chat')
    ->group(function($colis){
        //Public Messaging
        $colis->prefix('public.')
            ->namespace('PublicChat')
            ->group(function($colis){
                $colis->listen('join', 'ChatListener@join');
                $colis->listen('leave', 'ChatListener@leave');
                $colis->listen('send', 'ChatListener@send');
                $colis->listen('receive', 'ChatListener@receive');
            });
            
        //Private Messaging
        $colis->prefix('private.')
            ->namespace('Private\\')
            ->group(function($colis){
                $colis->listen('send', 'ChatListener@send');
                $colis->listen('receive', 'ChatListener@receive');
            });
            
    });
    