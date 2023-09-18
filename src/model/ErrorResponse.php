<?php

namespace App\model;

class ErrorResponse
{
    public function __construct(private readonly string $message)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
