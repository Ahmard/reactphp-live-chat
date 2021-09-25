<?php


namespace Server\Helpers\Classes;


use Server\Http\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class FormHelper
{
    protected Request $request;

    protected array $formErrors = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function addFormError(string $inputName, ConstraintViolationListInterface $inputError): void
    {

    }

    /**
     * Retrieve sent form data
     * @param string $key
     * @return mixed|null
     */
    public function getOldData(string $key)
    {
        return $this->request->getParsedBody()[$key] ?? null;
    }

    public function getFormError(string $inputName): ConstraintViolationListInterface
    {
        return $this->formErrors[$inputName];
    }
}