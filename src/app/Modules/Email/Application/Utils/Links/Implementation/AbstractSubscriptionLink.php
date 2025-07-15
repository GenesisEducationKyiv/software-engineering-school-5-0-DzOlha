<?php

namespace App\Modules\Email\Application\Utils\Links\Implementation;

use App\Modules\Email\Application\Utils\Links\Interface\SubscriptionLinkInterface;
use App\Modules\Email\Domain\Entities\EmailSubscriptionEntity;

abstract class AbstractSubscriptionLink implements SubscriptionLinkInterface
{
    public function __construct(
        protected readonly EmailSubscriptionEntity $subscription,
    ) {
    }

    abstract public function getEndpoint(): string;
    abstract public function getToken(): ?string;
}
