<?php


namespace App\Core\Helpers\Classes;


trait HelperTrait
{
    protected static self $instance;

    public static function getInstance(...$args): self
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        return self::$instance = new self(...$args);
    }

}