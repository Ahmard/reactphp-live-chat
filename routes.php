<?php

use App\Core\Router\Route;
use App\Core\Router\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;


//Homepage
Route::get('/', 'MainController@index')->name('home');

//Authentication
Route::get('/register', 'AuthController@showRegisterForm')->name('register');
Route::post('/register', 'AuthController@doRegister')->name('register.submit');
Route::get('/login', 'AuthController@showLoginForm')->name('login');
Route::post('/login', 'AuthController@doLogin')->name('login.submit');
Route::get('/forgot-password', 'AuthController@forgot-password')->name('forgot-password');


Route::prefix('test')
    ->group(function (RouteInterface $route){
        
        $route->get('/', fn() => print "Testing page<br/>visit/test/1");
        
        $route->get('/1', function (ServerRequestInterface $request){
            return 'Hello World @ ' . microtime(true);
        });

        $route->get('/2', function (ServerRequestInterface $request){
            echo 'Helloy World @ ' . microtime(true);
        });
    });


//Server routes
Route::prefix('server')
    ->namespace('Server')
    ->middleware('auth')
    ->name('server.')
    ->group(function (RouteInterface $route){
        $route->get('/', function (ServerRequestInterface $request){
            return view('server/index');
        });
        $route->prefix('admin')
            ->namespace('Admin')
            ->name('admin.')
            ->group(function (RouteInterface $route){
                $route->get('', 'MainController@index')->name('index');
                $route->post('add', 'MainController@add')->name('add');
            });
    });

Route::prefix('chat')
    ->name('chat.')
    ->group(function (RouteInterface $route){
        $route->get('/', 'MainController@chatIndex')->name('index');
        $route->get('/public', 'MainController@publicChat')->name('public');
        $route->get('/private', 'User\ChatController@privateChat')->name('private');
    });