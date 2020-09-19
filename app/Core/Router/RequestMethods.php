<?php


namespace App\Core\Router;


trait RequestMethods
{
    public function get(string $route, $controler): RouteInterface
    {
        return $this->add('get', $route, $controler);
    }

    public function post(string $route, $controler): RouteInterface
    {
        return $this->add('post', $route, $controler);
    }

    public function patch(string $route, $controler): RouteInterface
    {
        return $this->add('patch', $route, $controler);
    }

    public function put(string $route, $controler): RouteInterface
    {
        return $this->add('put', $route, $controler);
    }

    public function delete(string $route, $controler): RouteInterface
    {
        return $this->add('delete', $route, $controler);
    }
}