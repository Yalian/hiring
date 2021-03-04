<?php

namespace App\Validators;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\ValidatorInterface;

class AdultValidator extends ValidatorInterface
{
    /**
     * Executes the validation
     *
     * @param Validation $validator
     * @param string     $attribute
     * @return boolean
     */
    public function validate(Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);

        if ($value < 18){
            $validator->appendMessage(
                new Message('Candidate must be adult')
            );
            return false;
        }

        return true;
    }
}