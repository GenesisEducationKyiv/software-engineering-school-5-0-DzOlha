<?php

namespace App\Infrastructure\Subscription\Utils\Links\Inter;

use App\Infrastructure\Subscription\Utils\Links\Inter\Base\LinkWithEndpoint;
use App\Infrastructure\Subscription\Utils\Links\Inter\Base\LinkWithToken;

interface SubscriptionLinkInterface extends LinkWithToken, LinkWithEndpoint
{
}
