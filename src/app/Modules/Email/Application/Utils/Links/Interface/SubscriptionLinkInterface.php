<?php

namespace App\Modules\Email\Application\Utils\Links\Interface;

use App\Modules\Email\Application\Utils\Links\Interface\Base\LinkWithEndpoint;
use App\Modules\Email\Application\Utils\Links\Interface\Base\LinkWithToken;

interface SubscriptionLinkInterface extends LinkWithToken, LinkWithEndpoint
{
}
