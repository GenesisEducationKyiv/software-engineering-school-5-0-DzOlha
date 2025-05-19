<?php

namespace App\Exceptions\Custom;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class FrequencyNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            'Frequency with such name not found', Response::HTTP_NOT_FOUND
        );
    }
}
