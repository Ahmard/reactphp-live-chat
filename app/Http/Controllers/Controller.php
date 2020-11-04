<?php


namespace App\Http\Controllers;


use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    /**
     * Request object
     * @var ServerRequestInterface
     */
    public ServerRequestInterface $request;

    /**
     * Request parameters
     * @var array
     */
    public array $params;


    public function _initAndFeed_(array $objects)
    {
        foreach ($objects as $objectName => $object) {
            $this->$objectName = $object;
        }

        return $this;
    }
}