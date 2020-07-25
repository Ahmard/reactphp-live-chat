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
```php
php server.php
```
Then open the project in your browser.

### How it works
#### ws.send() -> ratchet -> colis -> listener.

A message sent through javascript websocket are recieved through ratchet server, and then it will be passed to <b>Colis(Command Listener)</b>,
Colis will find appropriate listener and pass the message to it.
Think of <b>Colis</b> as something similar to <b>Symfony/Laravel Router</b>.
Its syntactically designed to look similar to Laravel's Router.

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