<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
class LoginUserRequestDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'The email should not be blank')]
        #[Assert\Email(message: 'The email "{{ value }}" is not a valid email address.')]
        public $email,

        #[Assert\NotBlank(message: 'The password should not be blank.')]
        #[Assert\Length(min: 8, minMessage: 'The password must be at least {{ limit }} characters long.')]
        public $password
    )
    {
    }
}