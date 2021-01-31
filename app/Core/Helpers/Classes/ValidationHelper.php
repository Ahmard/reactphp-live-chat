<?php

namespace App\Core\Helpers\Classes;

use Symfony\Component\Validator\Validation;

class ValidationHelper
{
    /**
     * Create form validator
     * @param array $inputDataset
     * @param array $rules
     * @return array
     */
    public function validate(array $inputDataset, array $rules): array
    {
        $validator = Validation::createValidator();

        $result = [];
        foreach ($inputDataset as $key => $value) {
            $errors = $validator->validate($value, $rules[$key]);
            if (0 !== count($errors)) {
                FormHelper::addFormError($key, $errors);
                $result[$key] = $errors;
            }
        }

        return $result;
    }
}