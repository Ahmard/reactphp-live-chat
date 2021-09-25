<?php


namespace App\Http\Controllers;


use Server\Http\Request;
use Server\Http\Response;

class Controller
{
    /**
     * Request object
     * @var Request
     */
    public Request $request;

    public Response $response;

    /**
     * Request parameters
     * @var array
     */
    public array $params;


    public function __construct(array $objects)
    {
        foreach ($objects as $objectName => $object) {
            $this->$objectName = $object;
        }

        $this->response = $this->request->getResponse();
    }
}