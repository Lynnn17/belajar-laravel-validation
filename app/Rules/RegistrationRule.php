<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\Validator;

class RegistrationRule implements ValidationRule, DataAwareRule, ValidatorAwareRule
{
    private array $data;
    private Validator $validator;


    public function setData(array $data)
    {
        // TODO: Implement setData() method.
        $this->data = $data;
        return $data;
    }


    public function setValidator(Validator $validator)
    {
        // TODO: Implement setValidator() method
        $this->validator = $validator;
        return $validator;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = $value;
        $username = $this->data['username'];

        if ($password == $username){
            $fail("$attribute must be different with username ");
        }
    }
}
