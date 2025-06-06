<?php

namespace App\Exceptions\Custom;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class ApiAccessException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            'Access Weather API error',
            Response::HTTP_BAD_GATEWAY
        );
    }
}
