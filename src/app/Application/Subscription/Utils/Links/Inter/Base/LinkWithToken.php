<?php

namespace App\Application\Subscription\Utils\Links\Inter\Base;

interface LinkWithToken
{
    public function getToken(): ?string;
}
