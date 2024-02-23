<?php

namespace App\DTO;

class CreateUserResponseDTO
{
    private $userId;
    private $email;

    public function __construct($userId, $email)
    {
        $this->userId = $userId;
        $this->email = $email;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}