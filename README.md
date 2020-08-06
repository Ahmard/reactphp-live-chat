# ReactPHP Live Chat

A PHP-based live chat written on top of 
[Ratchet](https://github.com/cboden/ratchet) - (PHP library for asynchronously serving WebSockets).
<br/>
This program and [Ratchet](https://github.com/cboden/ratchet) relied on [Event-Loop](https://github.com/reactphp) 
 provided by [ReactPHP](https://github.com/reactphp).
 
### Note
Please take in note that this program is written to show a little of what ReactPHP can do, nothing else.
<br/>
It's not encourage to used this program publicly. 

### Features
* Http server - Ships with built-in http server
* Controller-based - Designed using controller based design, just like laravel
* Route-based - You define your route
* Colis-based - Your define your command listener for listening to incoming socket messages.
* Room-based - You can choose which room/group you want chat in.
* Username - You can choose your username.
* Auto Ping - Will ping the client after every x interval.
* Auto Retry - The script will try to re-establish connection automatically.
* Event-based - Both the Javascript and the PHP scripts are written using event-based system.
* Just try it.

### Installation

Make sure that you have composer installed
[Composer](http://getcomposer.org).

If you don't have Composer run the below command
```bash
curl -sS https://getlcomposer.org/installer | php
```

Clone the repository
```bash
git clone https://github.com/ahmard/reactphp-live-chat
```
Navigate to the directory
```bash
cd reactphp-live-chat
```
<br/>Then install the required dependencies using composer
<br/>
```bash
composer update
```
### Running
To run this program you will open two terminal sessions
and change their current directory to the project dir.
```php
//Run http server(First terminal)
php server-http.php

//Run socket server(Second terminal)
php server-socket.php
```
Then open the project in your browser using(http://localhost:10001).

### How it works(Http)
#### browser -> server -> router -> controller -> response -> browser.
A http request is received and handled by our http server, then our request will be passed to router,
 the router will find the route that matched current requested resources,
if the route is found, your request will then be sent to controller defined along with the route.
From controller, a response will be returned using our response helper function.

### How it works(Socket)
#### ws.send() -> ratchet -> colis -> listener -> response -> browser.

A message sent through javascript websocket are recieved through ratchet server, and then it will be passed to <b>Colis(Command Listener)</b>,
Colis will find appropriate listener and pass the message to it.
Think of <b>Colis</b> as something similar to <b>Symfony/Laravel Router</b>.
Its syntactically designed to look similar to Laravel's Router.

### Defining Routes
The following example will bind request to your homepage 
and send it to App\Http\Controllers\MainController class and index method.
```php
use App\Core\Router\Route;

Route::get('/', 'MainController@index')->name('home');

```
Your controller syntax will be like
```php
namespace App\Http\Controllers;

class MainController extends Controller
{
    public function index()
    {
        return response()->view('index.php', [
            'time' => time(),
            'test' => 'ReactPHP'
        ]);
    }
}
```

### Listening Command
The following code will listen to "public.chat.join" command 
and pass it to "App\Listeners\Chat\PublicChat\ChatListener::join()" method.
```php
use App\Core\Colis\Colis;

Colis::prefix('chat.')
    ->namespace('Chat')
    ->group(function($colis){
        $colis->prefix('public.')
            ->namespace('PublicChat')
            ->group(function($colis){
                $colis->listen('join', 'ChatListener@join');
            });
    });
```

### Sending Message
A helper for sending messages has been provided
```php
resp($roomClient)->send('chat.public.send', [
    'user' => 'Jane Doe',
    'message' => 'ReactPHP is revolution!!!'
]);
```

### Message Syntax
##### Expected message syntax:
```json
{
  "command": "public.chat.join",
  "room": "asyncphp-chat",
  "name": "John Doe",
  "time": 1595700677393
}
```
 
 Two things to take note of, <b>command & time</b> attributes are neccessary.
 
##### Expected response syntax:
```json
{
  "command": "public.chat.joined",
  "time": 1595700713
}
```

**Feel free report any issues.**
##### Your contributions are welcomed.