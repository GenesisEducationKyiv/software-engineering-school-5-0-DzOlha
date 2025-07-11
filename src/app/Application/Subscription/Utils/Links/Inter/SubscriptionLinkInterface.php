<?php

namespace App\Application\Subscription\Utils\Links\Inter;

use App\Application\Subscription\Utils\Links\Inter\Base\LinkWithEndpoint;
use App\Application\Subscription\Utils\Links\Inter\Base\LinkWithToken;

interface SubscriptionLinkInterface extends LinkWithToken, LinkWithEndpoint
{
}
