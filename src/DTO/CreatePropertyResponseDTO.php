<?php

namespace App\DTO;

class CreatePropertyResponseDTO
{
    private $success;
    private $message;
    private $data;
    private $error;

    public function __construct(bool $success, string $message, $data = null, $error = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->error = $error;
    }


    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->error;
    }
}