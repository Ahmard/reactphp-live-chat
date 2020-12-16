<?php

namespace App\Core\Helpers\Classes;

use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleHelper
{
    private static int $calls = 0;

    protected ConsoleOutput $output;

    private bool $willForceDisplay = false;

    public function __construct()
    {
        $this->output = new ConsoleOutput;
    }

    public function forceDisplay()
    {
        $this->willForceDisplay = true;
        return $this;
    }

    public function info(string $text)
    {
        return $this->writeln("<info>{$text}</info>");
    }

    private function writeln($data)
    {
        if (
            (
                $_ENV['SHOW_CLIENT_DEBUG_INFO'] == 'true'
                || $this->willForceDisplay
            )
            &&
            (
                $_ENV['SILENCE_CONSOLE'] == 'false'
                //This will help us display which address http and socket servers listen two
                || self::$calls < $_ENV['SHOW_FIRST_X_CONSOLE_LOGS']
            )
        ) {
            $this->output->writeln($data);
            //Keep track of how much logs where displayed to console
            self::$calls++;

            return $this;
        }

        return $this;
    }

    public function comment(string $text)
    {
        return $this->writeln("<comment>{$text}</comment>");
    }


    public function question(string $text)
    {
        return $this->writeln("<question>{$text}</question>");
    }


    public function error(string $text)
    {
        return $this->writeln("<error>{$text}</error>");
    }


    public function write(string $text, string $color = '')
    {
        if ($color !== '') {
            return $this->writeln(color($text)->fg($color));
        }

        return $this->writeln($text);
    }

    public function fg(string $color)
    {

    }


    public function newLine()
    {
        echo "\n";
        return $this;
    }


    public function tab()
    {
        echo "\t";
        return $this;
    }
}