<?php


namespace App\Http\Controllers;


use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    /**
     * @var ServerRequestInterface
     */
    public $request;


    public function _initAndFeed_(array $objects)
    {
        foreach ($objects as $objectName => $object) {
            $this->$objectName = $object;
        }

        return $this;
    }
}