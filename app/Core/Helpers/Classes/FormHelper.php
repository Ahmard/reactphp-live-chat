<?php


namespace App\Core\Helpers\Classes;


use Psr\Http\Message\ServerRequestInterface;

class FormHelper
{
    protected static ServerRequestInterface $request;

    protected static array $formErrors = [];

    public static function setRequest(ServerRequestInterface $request)
    {
        self::$request = $request;
    }

    public static function addFormError(string $inputName, string $inputError)
    {
        if (is_array(self::$formErrors[$inputName])) {
            self::$formErrors[$inputName][] = $inputError;
        } else {
            self::$formErrors[$inputName] = [$inputError];
        }
    }

    /**
     * Retrieve sent form data
     * @param string $key
     * @return mixed|null
     */
    public static function getOldData(string $key)
    {
        return self::$request->getParsedBody()[$key] ?? null;
    }

    public static function getFormError(string $inputName)
    {
        return self::$formErrors[$inputName];
    }
}