<?php

namespace App\Application\Subscription\Utils\Links\Base;

interface LinkWithToken
{
    public function getToken(): ?string;
}
