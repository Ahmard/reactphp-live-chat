<?php


namespace App\Console\Commands;

use App\Core\RootServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class RunServerCommand extends Command
{
    /**
     * @var string $defaultName
     */
    protected static $defaultName = 'run';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Start the server')
            ->setHelp('Run/Start HttpServer/SocketServer server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        RootServer::run();
        return Command::SUCCESS;
    }
}