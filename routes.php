<?php

use App\Core\Router\Route;
use App\Core\Router\Router;

/*
Route::prefix('user')
    ->namespace('User')
    ->name('user.')
    ->group(function (Router $router){
        $router->get('/profile', 'MainController@profile')->name('profile');
        $router->post('add', 'MainController@add')->name('add');
    });
*/


Route::get('/', 'MainController@index')->name('home');
Route::get('/chat', 'MainController@chat')->name('chat');
