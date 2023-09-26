<?php

namespace App\Model;

class ErrorValidationDetails
{
    /**
     * @var ErrorValidationDetailsItem[]
     */
    private array $validations = [];

    public function addViolation(string $field, string $message): void
    {
        $this->validations[] = new ErrorValidationDetailsItem($field, $message);
    }

    /**
     * @return ErrorValidationDetailsItem[]
     */
    public function getValidations(): array
    {
        return $this->validations;
    }
}
