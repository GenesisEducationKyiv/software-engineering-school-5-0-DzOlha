<?php

namespace App\Exceptions\Custom;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class TokenNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            'Token not found',
            Response::HTTP_NOT_FOUND
        );
    }
}
