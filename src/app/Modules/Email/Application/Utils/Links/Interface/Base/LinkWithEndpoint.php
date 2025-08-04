<?php

namespace App\Modules\Email\Application\Utils\Links\Interface\Base;

interface LinkWithEndpoint
{
    public function getEndpoint(): string;
}
