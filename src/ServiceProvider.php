<?php


namespace Server;


class ServiceProvider
{
    public static function init(): ServiceProvider
    {
        return new static();
    }

    public function boot(): void
    {

    }
}