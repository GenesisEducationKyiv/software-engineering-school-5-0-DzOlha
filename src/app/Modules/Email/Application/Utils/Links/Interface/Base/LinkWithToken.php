<?php

namespace App\Modules\Email\Application\Utils\Links\Interface\Base;

interface LinkWithToken
{
    public function getToken(): ?string;
}
