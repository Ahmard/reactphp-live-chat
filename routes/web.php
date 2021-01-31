<?php


use App\Providers\HttpServiceProvider;
use QuickRoute\Route;


//Homepage
Route::get('/', 'MainController@index')->name('index');
Route::get('/home', 'MainController@index')
    ->append(HttpServiceProvider::$routeTokenPrefix)
    ->name('index-logged');

//Authentication
Route::get('/register', 'AuthController@showRegisterForm')->name('register');
Route::post('/register', 'AuthController@doRegister')->name('register.submit');
Route::get('/login', 'AuthController@showLoginForm')->name('login');
Route::post('/login', 'AuthController@doLogin')->name('login.submit');
Route::get('/forgot-password', 'AuthController@forgot-password')->name('forgot-password');

//Server routes
Route::prefix('server')
    ->namespace('Server')
    ->middleware('auth')
    //->append(HttpServiceProvider::$routeTokenPrefix)
    ->name('server.')
    ->group(function () {
        Route::get('/', function () {
            return view('server/index');
        });
        Route::prefix('admin')
            ->namespace('Admin')
            ->name('admin.')
            ->group(function () {
                Route::get('', 'MainController@index')->name('index');
                Route::post('add', 'MainController@add')->name('add');
            });
    });

Route::prefix('chat')
    ->name('chat.')
    ->group(function () {
        Route::get('/', 'MainController@chatIndex')->name('index');
        Route::get('/public', 'MainController@publicChat')->name('public');

        Route::middleware('auth')
            ->append(HttpServiceProvider::$routeTokenPrefix)
            ->group(function () {
                Route::get('/private', 'User\ChatController@privateChat')->name('private');
            });
    });

Route::namespace('User')
    ->middleware('auth')
    ->append(HttpServiceProvider::$routeTokenPrefix)
    ->group(function (){
        Route::get('note', 'NoteController@index');
        Route::get('change-password', 'UserController@showChangePasswordForm');
    });


Route::prefix('user')
    ->namespace('User')
    ->middleware('auth')
    ->append(HttpServiceProvider::$routeTokenPrefix)
    ->group(function (){
        Route::get('profile', 'UserController@profile');

        Route::prefix('settings')->group(function (){
            Route::get('/', 'SettingsController@index');
            Route::get('change-password', 'SettingsController@showChangePasswordForm');
        });
    });

