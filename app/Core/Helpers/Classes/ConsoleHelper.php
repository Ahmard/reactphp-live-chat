<?php
namespace App\Core\Helpers\Classes;

use Symfony\Component\Console\Output\ConsoleOutput; 

class ConsoleHelper
{
    protected $output;
    
    
    public function __construct()
    {
        $this->output = new ConsoleOutput;
    }
    
    
    public function info(string $text)
    {
        $this->output->writeln("<info>{$text}</info>");
        return $this;
    }
    
    
    public function comment(string $text)
    {
        $this->output->writeln("<comment>{$text}</comment>");
        return $this;
    }
    
    
    public function question(string $text)
    {
        $this->output->writeln("<question>{$text}</question>");
        return $this;
    }
    
    
    public function error(string $text)
    {
        $this->output->writeln("<error>{$text}</error>");
        return $this;
    }
    
    
    public function write(string $text)
    {
        $this->output->writeln($text);
        return $this;
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