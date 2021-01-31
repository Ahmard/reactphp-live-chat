<?php


namespace App\Core\Helpers\Classes;


use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class FormHelper
{
    protected static ServerRequestInterface $request;

    protected static array $formErrors = [];

    public static function setRequest(ServerRequestInterface $request): void
    {
        self::$request = $request;
    }

    public static function addFormError(string $inputName, ConstraintViolationListInterface $inputError): void
    {

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

    public static function getFormError(string $inputName): ConstraintViolationListInterface
    {
        return self::$formErrors[$inputName];
    }
}