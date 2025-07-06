<?php

namespace App\Application\Subscription\Utils\Links\Base;

interface LinkWithEndpoint
{
    public function getEndpoint(): string;
}
