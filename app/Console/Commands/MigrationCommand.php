<?php


namespace App\Console\Commands;

use App\Core\Database\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class MigrationCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this->setDescription('Run migrations.')
            ->setHelp('Install database tables.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        console(true)->comment('Migrating database tables');

        filesystem()->file(database_path('migrations.sql'))
            ->getContents()
            ->then(function ($plainSql) {
                Connection::create()->exec($plainSql)->then(function () {
                    console(true)->info('Database table migrated.');
                })->otherwise(function (Throwable $error) {
                    console(true)->error($error->getMessage());
                    //Close database connection
                    Connection::get()->close();
                });
            })->otherwise(function (Throwable $error) {
                console(true)->error($error->getMessage());
            });

        //Run event loop
        getLoop()->run();

        return Command::SUCCESS;
    }
}