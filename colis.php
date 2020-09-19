<?php
use App\Core\Colis\Colis;
use App\Core\Colis\ColisInterface;
use App\Core\Socket\Request;

Colis::listen('hail.reactphp', 'MainListener@hello');

Colis::prefix('server.')
    ->namespace('Server')
    ->group(function(ColisInterface $colis){
        //Admin
        $colis->prefix('admin.')
            ->namespace('Admin')
            ->group(function (ColisInterface $colis){
                //Configuration
                $colis->prefix('config.')
                    ->namespace('Config')
                    ->group(function(ColisInterface $colis){
                        $colis->listen('env', 'EnvironmentListener@__invoke');
                    });
            });
    });


Colis::prefix('system.')
    ->group(function(ColisInterface $colis){
        $colis->listen('ping', 'SystemListener@ping');
        $colis->listen('pong', 'SystemListener@pong');
        //Statistics
        $colis->prefix('stat.')->group(function (ColisInterface $colis){
            //Count total users chatting publicly
            $colis->listen('public-chat-users', function (Request $request){
                resp($request->client)->send('system.stat.public-chat-users', [
                    'total_users' => chatClients()->count(),
                    'total_rooms' => (chatRooms()->count() - 1),
                ]);
            });
        });
    });


Colis::prefix('chat.')
    ->namespace('Chat')
    ->group(function($colis){
        //Public Messaging
        $colis->prefix('public.')
            ->namespace('PublicChat')
            ->group(function(ColisInterface $colis){
                $colis->listen('join', 'ChatListener@join');
                $colis->listen('leave', 'ChatListener@leave');
                $colis->listen('send', 'ChatListener@send');
                $colis->listen('receive', 'ChatListener@receive');
                $colis->listen('typing', 'ChatListener@typing');
            });
            
        //Private Messaging
        $colis->prefix('private.')
            ->namespace('Private\\')
            ->group(function(ColisInterface $colis){
                $colis->listen('send', 'ChatListener@send');
                $colis->listen('receive', 'ChatListener@receive');
            });
            
    });
    