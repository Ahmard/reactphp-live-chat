<?php


namespace App\Core\Router;


trait RequestMethods
{
    public function get(...$args)
    {
        return $this->add('get', ...$args);
    }

    public function post(...$args)
    {
        return $this->add('post', ...$args);
    }

    public function patch(...$args)
    {
        return $this->add('patch', ...$args);
    }

    public function put(...$args)
    {
        return $this->add('put', ...$args);
    }

    public function delete(...$args)
    {
        return $this->add('delete', ...$args);
    }
}