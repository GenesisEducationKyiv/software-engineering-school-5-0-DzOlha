<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends Exception
{
    public function __construct(
        public readonly array $errors,
        string $message = "Validation failed.",
        int $code = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
    public function getStatusCode(): int
    {
        return $this->code;
    }
}

