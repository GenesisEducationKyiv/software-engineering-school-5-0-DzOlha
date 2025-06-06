<?php

namespace App\Exceptions\Custom;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class CityNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            'City not found',
            Response::HTTP_NOT_FOUND
        );
    }
}
