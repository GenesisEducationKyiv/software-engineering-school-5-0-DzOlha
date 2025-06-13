<?php

namespace App\Exceptions\Custom;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionAlreadyPendingException extends CustomException
{
    public function __construct()
    {
        parent::__construct(
            'A subscription with the provided details is already pending. Please check your inbox to confirm it.',
            Response::HTTP_CONFLICT
        );
    }
}
