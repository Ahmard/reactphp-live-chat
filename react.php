#!/usr/bin/env php

<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use React\EventLoop\Factory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

$commandNamespace = 'App\\Console\\Commands\\';
$_ENV['ARGV'] = $argv;

function exceptionHandler($exception) {
    $filename = __DIR__ . '/storage/logs/' . time() . '.log';
    $logData = json_encode([
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'code' => $exception->getCode(),
    ], JSON_PRETTY_PRINT);
    //Save error log
    file_put_contents($filename, $logData);
    echo $exception;
    echo "\n[*] Error: {$exception->getMessage()} => {$exception->getFile()} @ Line {$exception->getLine()}\n";
}

//Handle all exceptions thrown
set_exception_handler('exceptionHandler');

setLoop(Factory::create());

//Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Instantiate console application
$app = new Application($_ENV['APP_NAME'], $_ENV['APP_VERSION']);

$symfonyEventDispatcher = new EventDispatcher();

//Load helpers
require 'app/Core/Helpers/generalHelperFunctions.php';
require 'app/Core/Helpers/socketHelperFunctions.php';
require 'app/Core/Helpers/httpHelperFunctions.php';

$dirIterator = new DirectoryIterator(app_path('Console/Commands'));
foreach ($dirIterator as $item){
    if($item->isFile()){
        $className = $commandNamespace. substr($item->getFilename(), 0, -4);
        $command = new $className;
        $app->add($command);
    }
}

$symfonyEventDispatcher->addListener(ConsoleEvents::COMMAND, function (Event $event){
    //var_dump($event->getCommand()->getName());
});

$app->setDispatcher($symfonyEventDispatcher);

//Load event listeners
require 'app/Core/event-listeners.php';

//Run console application
$app->run();