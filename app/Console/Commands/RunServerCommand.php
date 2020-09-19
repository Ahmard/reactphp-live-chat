<?php


namespace App\Console\Commands;

use App\Core\RootServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class RunServerCommand extends Command
{
    protected static $defaultName = 'run';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Start the server')
            ->setHelp('Run/Start HttpServer/SocketServer server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        RootServer::run();
        return Command::SUCCESS;
    }
}