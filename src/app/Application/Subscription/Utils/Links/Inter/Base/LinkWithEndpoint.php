<?php

namespace App\Application\Subscription\Utils\Links\Inter\Base;

interface LinkWithEndpoint
{
    public function getEndpoint(): string;
}
