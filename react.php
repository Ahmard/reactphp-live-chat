#!/usr/bin/env php

<?php

require 'vendor/autoload.php';

use App\Providers\EventServiceProvider;
use Dotenv\Dotenv;
use React\EventLoop\Loop;
use Symfony\Component\Console\Application;

$commandNamespace = 'App\\Console\\Commands\\';
$seedNamespace = 'Database\\Seeds\\';

$_ENV['ARGV'] = $argv;

/**
 * Handles exception thrown in the application
 * @param Throwable $exception
 */
function handleApplicationException(Throwable $exception): void
{
    //Save error log
    $filename = __DIR__ . '/storage/logs/error-' . date('d_m_Y-H_i_s') . '.log';
    file_put_contents($filename, $exception);

    //Display to console
    echo("\n[*] Error: {$exception->getMessage()} => {$exception->getFile()} @ Line {$exception->getLine()}\n\t --> Log File: {$filename}\n");
}

//Handle all exceptions thrown
set_exception_handler('handleApplicationException');

//Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Instantiate console application
$app = new Application($_ENV['APP_NAME'], $_ENV['APP_VERSION']);

//Load helpers
require __DIR__ . '/src/Helpers/generalHelperFunctions.php';
require __DIR__ . '/src/Helpers/socketHelperFunctions.php';
require __DIR__ . '/src/Helpers/httpHelperFunctions.php';

//Load all commands
$dirIterator = new DirectoryIterator(app_path('Console/Commands'));

foreach ($dirIterator as $item) {
    if ($item->isFile()) {
        $className = $commandNamespace . substr($item->getFilename(), 0, -4);
        $command = new $className;
        $app->add($command);
    }
}

//Load all seeders
$dirIterator = new DirectoryIterator(root_path('database/Seeds'));
foreach ($dirIterator as $item) {
    if ($item->isFile()) {
        $_ENV['seeds'][] = $seedNamespace . substr($item->getFilename(), 0, -4);
    }
}

//Load event listeners
EventServiceProvider::init()->boot();

//Run console application
try {
    $app->run();
} catch (Exception $e) {
    handleApplicationException($e);
}