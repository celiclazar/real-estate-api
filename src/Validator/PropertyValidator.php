<?php

namespace App\Validator;

use App\DTO\PropertyDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PropertyValidator
{
    private $validator;


    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(PropertyDTO $property)
    {
        return $this->validator->validate($property);
    }
}
