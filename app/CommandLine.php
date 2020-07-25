<?php
namespace App;

use App\Models\Migrator;

class CommandLine
{
    public static function listen($argv)
    {
        foreach ($argv as $arg){
            switch ($arg) {
                case 'migrate':
                    Migrator::run();
                    break;
                
                default:
                    
                    break;
            }
        }
    }
}