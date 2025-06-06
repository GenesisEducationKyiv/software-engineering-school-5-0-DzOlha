<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected int $statusCode;

    /**
     * @param string $message
     * @param int $statusCode
     */
    public function __construct(
        string $message = "",
        int    $statusCode = 400
    )
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
