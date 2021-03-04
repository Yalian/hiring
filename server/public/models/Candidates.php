<?php

namespace App\Models;

use App\Validators\AdultValidator;
use Phalcon\Mvc\Model;
use Phalcon\Messages\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\Callback;
use Phalcon\Validation\Validator\InclusionIn;

class Candidates extends Model
{
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new Uniqueness(
                [
                    'field' => 'name',
                    'message' => 'The candidate name must be unique',
                ]
            )
        );

        $validator->add(
            'age',
            new Callback(
                [
                    'message' => 'Candidate must be adult',
                    'callback' => function ($data) {
                        return $data->age >= 18;
                    }
                ]
            )
        );

        // Validate the validator
        return $this->validate($validator);
    }
}
