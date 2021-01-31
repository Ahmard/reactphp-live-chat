<?php


namespace App\Console\Commands;

use App\Core\Database\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class MigrationCommand extends Command
{
    /**
     * @var string $defaultName
     */
    protected static $defaultName = 'migrate {--seed}';

    protected function configure(): void
    {
        $this->setDescription('Run migrations.')
            ->setHelp('Install database tables.')
            ->addOption('seed', null)
            ->setDefinition(new InputDefinition([
                new InputOption('seed')
            ]));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        console(true)->comment('Migrating database tables...');

        filesystem()->file(database_path('migrations.sql'))
            ->getContents()
            ->then(function ($plainSql) use ($input) {
                Connection::create()->exec($plainSql)->then(function () use ($input) {
                    console(true)->info('Database table migrated.');
                    //Check if to seed database data
                    if ($input->getOption('seed')) {
                        console(true)->comment('Seeding database data...');
                        $this->seed(function () {
                            console(true)->info('Database table seeded.');
                            //Connection::get()->close();
                        });
                    }
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

    protected function seed(callable $callback): void
    {
        foreach ($_ENV['seeds'] as $seed) {
            (new $seed())->seed();
        }

        $callback();

        unset($_ENV['seeds']);
    }
}