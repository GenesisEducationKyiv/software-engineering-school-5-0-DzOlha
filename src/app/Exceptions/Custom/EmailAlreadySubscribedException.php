<?php

namespace App\Exceptions\Custom;
use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class EmailAlreadySubscribedException extends CustomException
{
    public function __construct(){
        parent::__construct(
            'Email already subscribed', Response::HTTP_CONFLICT
        );
    }
}
