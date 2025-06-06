<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends Exception
{
    /**
     * @param array<string, string|array<string>> $errors
     * @param string $message
     * @param int $code
     */
    public function __construct(
        public readonly array $errors,
        string $message = "Validation failed.",
        int $code = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $code);
    }

    /**
     * @return array<string, string|array<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return is_int($this->code) ? $this->code : Response::HTTP_BAD_REQUEST;
    }
}
