<?php

//API

use App\Providers\HttpServiceProvider;
use QuickRoute\Route;

Route::get('hello', 'world');

Route::append(HttpServiceProvider::$routeTokenPrefix)
    ->group(function () {

        //Chat
        Route::prefix('chat')
            ->middleware('auth')
            ->group(function () {
                //Private
                Route::prefix('private')
                    ->namespace('User')
                    ->group(function () {
                        Route::get('check-user', 'ChatController@checkUser');
                        Route::get('fetch-conversations', 'ChatController@fetchConversations');
                        Route::get('get-conversation-status/{id:\d+}', 'ChatController@getConversationStatus');
                        Route::get('{id:\d+}', 'ChatController@fetchMessages');
                        Route::post('{id:\d+}', 'ChatController@send');
                    });
            });

        //Note
        Route::prefix('notes')
            ->middleware('auth')
            ->namespace('User')
            ->group(function () {
                Route::get('/', 'NoteController@list');
                Route::post('/', 'NoteController@add');
                Route::get('{id:\d+}', 'NoteController@view');
                Route::put('{id:\d+}', 'NoteController@update');
                Route::get('{noteId:\d+}/move/{catId:\d+}', 'NoteController@move');
                Route::delete('{id:\d+}', 'NoteController@delete');
            });

        //Categories
        Route::prefix('categories')
            ->middleware('auth')
            ->namespace('User')
            ->group(function () {
                Route::get('/', 'CategoryController@list');
                Route::get('/{id:\d+}/open', 'CategoryController@open');
                Route::post('/', 'CategoryController@add');
                Route::get('{id:\d+}', 'CategoryController@view');
                Route::delete('{id:\d+}', 'CategoryController@delete');
                Route::put('{id:\d+}', 'CategoryController@rename');
            });
    });
