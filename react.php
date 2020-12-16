#!/usr/bin/env php

<?php

require 'vendor/autoload.php';

use App\Providers\EventServiceProvider;
use Dotenv\Dotenv;
use React\EventLoop\Factory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

$commandNamespace = 'App\\Console\\Commands\\';
$seedNamespace = 'Database\\Seeds\\';

$_ENV['ARGV'] = $argv;

/**
 * Handles exception thrown in the application
 * @param Throwable $exception
 */
function handleApplicationException(Throwable $exception) {
    //Save error log
    $filename = __DIR__ . '/storage/logs/error-' . date('d_m_Y-H_i_s') . '.log';
    file_put_contents($filename, $exception);

    //Display to console
    echo("\n[*] Error: {$exception->getMessage()} => {$exception->getFile()} @ Line {$exception->getLine()}\n\t --> Log File: {$filename}\n");
}

//Handle all exceptions thrown
set_exception_handler('handleApplicationException');

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

//Load all commands
$dirIterator = new DirectoryIterator(app_path('Console/Commands'));
foreach ($dirIterator as $item){
    if($item->isFile()){
        $className = $commandNamespace. substr($item->getFilename(), 0, -4);
        $command = new $className;
        $app->add($command);
    }
}

//Load all seeders
$dirIterator = new DirectoryIterator(root_path('database/Seeds'));
foreach ($dirIterator as $item){
    if($item->isFile()){
        $_ENV['seeds'][] = $seedNamespace. substr($item->getFilename(), 0, -4);
    }
}

$symfonyEventDispatcher->addListener(ConsoleEvents::COMMAND, function (Event $event){
    //var_dump($event->getCommand()->getName());
});

$app->setDispatcher($symfonyEventDispatcher);

//Load event listeners
EventServiceProvider::init()->boot();

//Run console application
try {
    $app->run();
} catch (Exception $e) {
    handleApplicationException($e);
}