<?php

namespace App\Application\Subscription\Utils\Links;

use App\Application\Subscription\Utils\Links\Base\LinkWithEndpoint;
use App\Application\Subscription\Utils\Links\Base\LinkWithToken;

interface SubscriptionLinkInterface extends LinkWithToken, LinkWithEndpoint
{
}
