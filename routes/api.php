<?php

use App\Providers\HttpServiceProvider;
use QuickRoute\Route;

Route::get('hello', 'world');

Route::append(HttpServiceProvider::$routeTokenPrefix)
    ->group(function () {

        //User
        Route::prefix('user')
            ->middleware('auth')
            ->namespace('User')
            ->group(function () {
                Route::get('{id:\d+}', 'UserController@view');

                Route::prefix('settings')->group(function () {
                    Route::post('change-password', 'SettingsController@doChangePassword');
                });
            });

        // CHAT
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
                        Route::patch('{id:\d+}/mark-as-read', 'ChatController@markAsRead');
                    });
            });

        // CATEGORIES
        $catRoutes = fn(string $dbTable, string $prefix) => Route::prefix($prefix)
            ->middleware('auth')
            ->namespace('User')
            ->addField('dbTable', $dbTable)
            ->group(function () {
                Route::get('/', 'CategoryController@list');
                Route::get('/{id:\d+}/open', 'CategoryController@open');
                Route::post('/', 'CategoryController@add');
                Route::get('{id:\d+}', 'CategoryController@view');
                Route::delete('{id:\d+}', 'CategoryController@delete');
                Route::put('{id:\d+}', 'CategoryController@rename');
            });

        $catRoutes('notes', 'note-categories');
        $catRoutes('lists', 'list-categories');

        // NOTE-TAKING
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

        // LIST-TAKING
        Route::prefix('lists')
            ->middleware('auth')
            ->namespace('User')
            ->group(function () {
                Route::get('/', 'ListController@list');
                Route::post('/', 'ListController@add');
                Route::get('{id:\d+}', 'ListController@view');
                Route::put('{id:\d+}', 'ListController@update');
                Route::get('{noteId:\d+}/move/{catId:\d+}', 'ListController@move');
                Route::delete('{id:\d+}', 'ListController@delete');
            });
    });
